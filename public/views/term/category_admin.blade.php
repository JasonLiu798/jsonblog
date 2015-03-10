@include('templates/header')

{{ HTML::script('js/admin/admin.js') }}
{{ HTML::style('css/admin.css') }}


<div class="container">
<div class="row">
    @include('templates/sidebar_admin')
    <div class="col-md-5 col-sm-5 col-lg-5">
        <!-- <h3>{{ $title }}</h3> -->
        <div class="operations">
            <form method="post" action="{{url()}}/admin/category/batchdelete" accept-charset="utf-8" role="form" id="batch_delete_form">
                <input type="hidden" name="delete_ids" id="delete_ids" value=""/>
                <button class="btn btn-default" name="batchdelete" id="batchdelete">批量删除</button>
            </form>
        </div>

        <table class="table table-striped" >
            <tbody>
            <tr>
                <th width="10%"><input type="checkbox" id="selectall"></th>
                <th width="40%">分类名</th>
                <th width="20%">文章数</th>
                <!-- <th width="25%">回应给</th> -->
                <!-- <th width="10%">评论时间</th> -->
                <th width="20%">操作</th>
            </tr>
                @foreach($categories as $cat)
            <tr>
                <td>@if( $cat->term_id!=1 )<input type="checkbox" name="id" value="{{ $cat->term_id }}"/>@endif </td>

                <td>{{ $cat->name }}</td>
                <td>{{ $cat->post_cnt }}</td>
                <td>
                    @if( $cat->term_id!=1 )
                    <a href="{{url()}}/admin/category/delete/{{$cat->term_id}}">删除</a>|

                    <a href="{{url()}}/admin/category/update/{{$cat->term_id}}">修改</a>
                    @endif
                </td>
            </tr>
                @if( is_array($term->childs)&& count($term->childs)>0)
                    @foreach($term->childs as $child)
                        <tr>
                        <td>@if( $child->term_id!=1 )<input type="checkbox" name="id" value="{{ $child->term_id }}"/>@endif </td>

                        <td>{{ $child->name }}</td>
                        <td>{{ $child->post_cnt }}</td>
                        <td>
                            @if( $child->term_id!=1 )
                                <a href="{{url()}}/admin/category/delete/{{$child->term_id}}">删除</a>|

                                <a href="{{url()}}/admin/category/update/{{$child->term_id}}">修改</a>
                            @endif
                        </td>
                        </tr>
                    @endforeach
                @endif

                @endforeach

            </tbody>
            <!-- <tfoot><tr><td colspan="6">{{ '' /*$categories->links() */}}</td></tr> </tfoot> -->
        </table>
    </div>
    <div class="col-md-4 col-sm-4 col-lg-4">
        <h3>新建分类</h3>
        <form method="post" action="{{url()}}/admin/category/create" accept-charset="utf-8" role="form" id="create_post_form">
            <div class="form-group">
                <label for="new_category_name">分类名</label>
                <input type="text"  class="form-control" id="new_category_name" name="new_category_name" />
            </div>
            <div class="form-group">
                <label for="new_category_parent">父分类</label>
                <select class="form-control" name="new_category_parent" id="new_category_parent">
                    <option value="0">无</option>
                    @foreach($categories as $cat)
                        @if( $cat->term_id!=1 )
                            <option id="new_category_parent{{$cat->term_id}}" value="{{$cat->term_id }}">{{ $cat->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="save_new_category">添加</button>
                <button type="reset" class="btn btn-default" id="reset">重置</button>
            </div>
        </form>
    </div>

</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
