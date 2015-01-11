@if( is_null( Session::get('user')) )
    @include('templates/header_logout')
@else
    @include('templates/header_login')
@endif
<script>

</script>

<div class="container">
<div class="row">
    @include('templates/sidebar_admin')
    <div class="col-md-9 col-sm-9 col-lg-9">
        <h3>{{ $title }}</h3>
        <form method="post" action="{{url()}}/admin/category/update" accept-charset="utf-8" role="form" id="create_post_form">
            <input type="hidden" name="term_id" value="{{$cat->term_id}}"/>

            <div class="form-group">
                <label for="new_category_name">分类名</label>
                <input type="text"  class="form-control" id="new_category_name" name="new_category_name" value="{{$cat->name}}"/>
            </div>
            <div class="form-group">
                <label for="category_parent">父分类</label>
                <select class="form-control" name="category_parent" id="category_parent">
                    <option value="0">无</option>
                    @foreach($categories as $cate)
                        <option id="category_parent{{$cate->term_id}}" value="{{$cate->term_id }}" @if($cate->select==1) selected @endif>{{$cate->name}}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="save_new_category">添加</button>
                <button type="reset" class="btn btn-default" id="reset">重置</button>
            </div>
        </form>
        <a href="{{url()}}/admin/category">返回</a>
    </div>

</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
