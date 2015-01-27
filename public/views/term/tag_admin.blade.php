@include('templates/header')

{{ HTML::script('js/admin/admin.js') }}
{{ HTML::style('css/admin.css') }}


<div class="container">
<div class="row">
    @include('templates/sidebar_admin')
    <div class="col-md-5 col-sm-5 col-lg-5">
        <!-- <h3>{{ $title }}</h3> -->
        <div class="operations">
            <form method="post" action="{{url()}}/admin/tag/batchdelete" accept-charset="utf-8" role="form" id="batch_delete_form">
                <input type="hidden" name="delete_ids" id="delete_ids" value=""/>
                <button class="btn btn-default" name="batchdelete" id="batchdelete">批量删除</button>
            </form>
        </div>
        <table class="table table-striped" >
            <tbody>
            <tr>
                <th width="10%"><input type="checkbox" id="selectall"></th>
                <th width="40%">标签名</th>
                <th width="20%">文章数</th>
                <!-- <th width="25%">回应给</th> -->
                <!-- <th width="10%">评论时间</th> -->
                <th width="20%">操作</th>
            </tr>
                @foreach($tags as $tag)
            <tr>
                <td><input type="checkbox" name="id" value="{{ $tag->term_id }}"/></td>

                <td>{{ $tag->name }}</td>
                <td>{{ $tag->post_cnt }}</td>
                <td>
                    <a href="{{url()}}/admin/tag/delete/{{$tag->term_id}}">删除</a>|
                    <a href="{{url()}}/admin/tag/update/{{$tag->term_id}}">修改</a>
                </td>
            </tr>
                @endforeach

            </tbody>
            <!-- <tfoot><tr><td colspan="6">{{ '' /*$categories->links() */}}</td></tr> </tfoot> -->
        </table>
    </div>
    <div class="col-md-4 col-sm-4 col-lg-4">
        <h3>新建标签</h3>
        <form method="post" action="{{url()}}/admin/tag/create" accept-charset="utf-8" role="form" id="create_tag_form">
            <div class="form-group">
                <label for="new_tag_name">标签名</label>
                <input type="text"  class="form-control" id="new_tag_name" name="new_tag_name" />
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="save_new_tag">添加</button>
                <button type="reset" class="btn btn-default" id="reset">重置</button>
            </div>
        </form>
    </div>

</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
