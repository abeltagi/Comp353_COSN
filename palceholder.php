<!-- Allow the post owner to delete their own post -->
<?php if ($post['member_id'] === $user_id): ?>
    <form method="POST" action="posts.php" class="d-inline">
        <input type="hidden" name="delete_post_id" value="<?php echo $post['post_id']; ?>">
        <button type="submit" class="btn btn-danger btn-sm mt-2">Delete</button>
    </form>
<?php endif; ?>

<!-- Allow the group owner to delete posts with "Group Only" visibility -->
<?php if ($post['visibility'] === 'Group' && $post['group_owner_id'] === $user_id): ?>
    <form method="POST" action="posts.php" class="d-inline">
        <input type="hidden" name="delete_post_id" value="<?php echo $post['post_id']; ?>">
        <button type="submit" class="btn btn-danger btn-sm mt-2">Delete (Group Owner)</button>
    </form>
<?php endif; ?>