@extends(backpack_view('blank'))



@section('content')
    <div>
        <table id="detail"
               class="table table-bordered table-striped">
            <thead>
            <tr>
                <td>Class name</td>
                <td>Student</td>
            </tr>
            </thead>
            <tbody>

            @foreach($data as $item)
                <tr>

                    <td>{{ $item->name }}</td>
                    <td>
                        @foreach(\App\Models\Students::query()->where('classid',$item['id'])->get() as $key=>$std)


                            <div class="row">
                                <div class="col">
                                    <p>{{$std->name}}

                                </div>
                                <div class="col">
                                    <p>{{$std->email}}
                                </div>
                                <div class="col">
                                    <a href="/admin/students/{{$std->id}}/edit" type="button"
                                                    class="btn btn-sm btn-success">Edit</a>
                                    <a href="/admin/students/{{$std->id}}/show" type="button"
                                       class="btn btn-sm btn-primary">Show</a>
                                </div>
                            </div>
                            @if($key != sizeof(\App\Models\Students::query()->where('classid',$item['id'])->get())-1)
                                <hr>
                            @endif


                        @endforeach
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>
@endsection
