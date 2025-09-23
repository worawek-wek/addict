@forelse($staffData as $index => $staff)
<tr>
    <td class="text-center">{{ $index + 1 }}</td>
    <td class="text-center">
        {{ $staff['name'] }}
        @if(!empty($staff['nickname']))
            ({{ $staff['nickname'] }})
        @endif
    </td>
    <td class="text-center">{{ $staff['branch'] ?? '-' }}</td>
    <td class="text-center">{{ $staff['position'] ?? '-' }}</td>
    <td class="text-center">
        {{ number_format($staff['commission'], 2) }} บาท
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center">- ไม่มีข้อมูล -</td>
</tr>
@endforelse
