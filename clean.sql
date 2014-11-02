
--清理已经不存在的博文相关标签
delete from term_relationships where object_id not in (select ID from posts);
commit;
--清理已经不存在的博文相关评论
delete from comments where comment_post_ID not in (select ID from posts);
commit;