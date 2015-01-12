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
        <form method="post" action="{{url()}}/admin/tag/update/{{$tag->term_id}}" accept-charset="utf-8" role="form" id="create_post_form">
            <input type="hidden" name="method" value="update"/>

            <div class="form-group">
                <label for="tag_name">分类名</label>
                <input type="text"  class="form-control" id="tag_name" name="tag_name" value="{{$tag->name}}"/>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="update_tag">修改</button>
                <button type="reset" class="btn btn-default" id="reset">重置</button>
            </div>
        </form>
        <a href="{{url()}}/admin/tag">返回</a>
    </div>

</div><!-- end of row -->
</div><!-- end of container ng-app controller -->
@include('templates/footer')
