<table class="table">
    <thead>
    <tr>
        @foreach($headers as $header)
            <th>{{ $header }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
    <tr>
        @foreach($row as $item)
        <td>{!! $item !!}</td>
        @endforeach
    </tr>
    @endforeach
    </tbody>
</table>
@if(isset($page)&&$page)
    @if(isset($params))
        <?php echo $page->appends($params)->render(); ?>
    @else
        <?php echo $page->render(); ?>
    @endif
@endif