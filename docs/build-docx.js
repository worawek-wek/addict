// สร้างคู่มือ .docx จากไฟล์ .md ในโฟลเดอร์นี้ (ฝังภาพจาก docs/images/)
// วิธีรัน:  cd docs && npm init -y && npm install docx@8 && node build-docx.js
// แก้ .md แล้วรันใหม่ทุกครั้ง  |  ฟอนต์ TH Sarabun New
const fs = require('fs');
const path = require('path');
const {
  Document, Packer, Paragraph, TextRun, HeadingLevel,
  ImageRun, AlignmentType, BorderStyle,
} = require('docx');

const DOCS = '/Applications/XAMPP/xamppfiles/htdocs/addict/docs';
const IMG = path.join(DOCS, 'images');
const FONT = 'TH Sarabun New';

// per-doc map [ภาพ: caption] -> png file
let IMAGE_MAP = {};
function imageFor(caption) {
  const key = caption.trim();
  return IMAGE_MAP[key] || null;
}

function imgSize(file) {
  // fit width to ~600px page content, keep aspect from PNG header
  const buf = fs.readFileSync(path.join(IMG, file));
  const w = buf.readUInt32BE(16);
  const h = buf.readUInt32BE(20);
  const maxW = 600;
  const scale = Math.min(1, maxW / w);
  return { width: Math.round(w * scale), height: Math.round(h * scale) };
}

function run(text, opts = {}) {
  return new TextRun({ text, font: FONT, size: opts.size || 28, bold: !!opts.bold, color: opts.color });
}

function build(mdPath, outPath, docTitle) {
  const lines = fs.readFileSync(mdPath, 'utf8').split('\n');
  const children = [];

  for (let raw of lines) {
    const line = raw.replace(/\s+$/, '');
    const trimmed = line.trim();

    if (trimmed === '' ) { continue; }
    if (trimmed === '---') { continue; }

    // image marker
    const m = trimmed.match(/^\[ภาพ:\s*(.+?)\]$/);
    if (m) {
      const file = imageFor(m[1]);
      if (file && fs.existsSync(path.join(IMG, file))) {
        const { width, height } = imgSize(file);
        children.push(new Paragraph({
          alignment: AlignmentType.CENTER,
          spacing: { before: 120, after: 120 },
          children: [ new ImageRun({ data: fs.readFileSync(path.join(IMG, file)), transformation: { width, height } }) ],
        }));
        children.push(new Paragraph({
          alignment: AlignmentType.CENTER,
          spacing: { after: 200 },
          children: [ run('ภาพ: ' + m[1], { size: 24, color: '777777' }) ],
        }));
      }
      continue;
    }

    if (trimmed.startsWith('### ')) {
      children.push(new Paragraph({ heading: HeadingLevel.HEADING_3, spacing: { before: 200, after: 80 },
        children: [ run(trimmed.slice(4), { bold: true, size: 30 }) ] }));
      continue;
    }
    if (trimmed.startsWith('## ')) {
      children.push(new Paragraph({ heading: HeadingLevel.HEADING_2, spacing: { before: 300, after: 120 },
        children: [ run(trimmed.slice(3), { bold: true, size: 34, color: '1F4E79' }) ] }));
      continue;
    }
    if (trimmed.startsWith('# ')) {
      children.push(new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 240 },
        children: [ run(trimmed.slice(2), { bold: true, size: 44, color: '1F4E79' }) ] }));
      continue;
    }

    // bullets (support one nested level via leading spaces on raw)
    const bulletMatch = line.match(/^(\s*)-\s+(.*)$/);
    if (bulletMatch) {
      const indent = bulletMatch[1].length;
      const level = indent >= 2 ? 1 : 0;
      children.push(new Paragraph({
        bullet: { level },
        spacing: { after: 40 },
        children: parseInline(bulletMatch[2]),
      }));
      continue;
    }

    // normal paragraph
    children.push(new Paragraph({ spacing: { after: 100 }, children: parseInline(trimmed) }));
  }

  const doc = new Document({
    creator: 'Addict', title: docTitle,
    styles: { default: { document: { run: { font: FONT, size: 28 } } } },
    sections: [{ properties: { page: { margin: { top: 900, bottom: 900, left: 1000, right: 1000 } } }, children }],
  });

  return Packer.toBuffer(doc).then(buf => fs.writeFileSync(outPath, buf));
}

// bold **...** and inline code `...`
function parseInline(text) {
  const runs = [];
  const parts = text.split(/(\*\*[^*]+\*\*|`[^`]+`)/g);
  for (const p of parts) {
    if (!p) continue;
    if (p.startsWith('**') && p.endsWith('**')) runs.push(run(p.slice(2, -2), { bold: true }));
    else if (p.startsWith('`') && p.endsWith('`')) runs.push(new TextRun({ text: p.slice(1, -1), font: 'Consolas', size: 26, color: 'B03A2E' }));
    else runs.push(run(p));
  }
  return runs.length ? runs : [ run(text) ];
}

const JOBS = [
  {
    md: 'commission-user-guide.md', out: 'commission-user-guide.docx',
    title: 'คู่มือระบบคอมมิชชั่นทีมมาม่าแบบ Rank',
    map: {
      'หน้าตั้งค่าบันได Rank': 'rank-setting.png',
      'หน้าแก้ไขบุคลากร ช่องเลือกโหมด': 'staff-mode.png',
      'หน้ารายงานค่าคอม': 'report.png',
      'หน้า Dashboard สรุปคอมมิชชั่น': 'dashboard.png',
    },
  },
  {
    md: 'attendance-user-guide.md', out: 'attendance-user-guide.docx',
    title: 'คู่มือระบบลงเวลาเข้างาน',
    map: {
      'หน้าแตะบัตรเข้างาน': 'clock-in.png',
      'หน้ารายชื่อการเข้างาน': 'attendance-list.png',
      'หน้ารายงานการเข้างาน': 'attendance-report.png',
    },
  },
];

(async () => {
  for (const j of JOBS) {
    IMAGE_MAP = j.map;
    await build(path.join(DOCS, j.md), path.join(DOCS, j.out), j.title);
    console.log('built ' + j.out);
  }
})().catch(e => { console.error(e); process.exit(1); });
