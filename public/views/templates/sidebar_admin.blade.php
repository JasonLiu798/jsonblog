<!--col-sm-offset-1 col-md-offset-1 col-lg-offset-1-->
<div class="col-sm-3 col-md-3 col-lg-3  blog-sidebar">
	<a href="{{url()}}/admin/post"><h3>后台管理</h3></a>
    <div class="list-group">

        <a href="{{url()}}/admin/post" class="list-group-item @if($menu==='post') active @endif">文章</a>
        <a href="{{url()}}/admin/comment" class="list-group-item @if($menu==='comment') active @endif" >评论|留言</a>
        <a href="{{url()}}/admin/category" class="list-group-item @if($menu==='category') active @endif">分类</a>
        <a href="{{url()}}/admin/tag" class="list-group-item @if($menu==='tag') active @endif">标签</a>
		<a href="{{url()}}/admin/image" class="list-group-item @if($menu==='img') active @endif">图片</a>
    </div>
</div>
