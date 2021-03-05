<table>
    <thead style="font-weight:bold;">
        <tr>
            <th width="30">Name</th>
            <th width="20">Score</th>
            <th width="20">Passing Score</th>
            <th width="20">Status</th>
            <th width="20">Start Time</th>
            <th width="20">Completion Time</th>
            <th width="20">Consumed Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach($result as $row)
        <tr>
            <td>{{ $row->trainee_name }}</td>
            <td>{{ $row->score }} / {{ $row->items }}</td>
            <td>{{ $row->passing_score }}</td>
            <td>{{ $row->score >= $row->passing_score ? 'passed' : 'failed' }}</td>
            <td>{{ $row->start_time }}</td>
            <td>{{ $row->completion_time }}</td>
            <td>{{ $row->consumed_time }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
