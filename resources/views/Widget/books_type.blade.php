<select name="{{$_other['name']}}" class="form-control" id="{{$_other['name']}}">
    <option value="-1">请选择分类</option>
    @foreach($_list as $vo)
        <option value="{{$vo->id}}" {{$_other['id']==$vo->id?'selected':''}}>{{$vo->type_name}}</option>
    @endforeach
</select>