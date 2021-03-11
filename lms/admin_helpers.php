<?php
function stm_lms_add_theme_caps()
{

	$admin_users = $instructors = array();
	$admin_users[] = get_role('administrator');
	$instructors[] = get_role('stm_lms_instructor');

	if (!empty($admin_users)) {
		foreach ($admin_users as $user) {
			if (empty($user)) continue;
			foreach (array('publish', 'delete', 'delete_others', 'delete_private', 'delete_published', 'edit', 'edit_others', 'edit_private', 'edit_published', 'read_private') as $cap) {
				$user->add_cap("{$cap}_stm_lms_posts");
			}
		}
	}

	if (!empty($instructors)) {
		foreach ($instructors as $user) {
			if (empty($user)) continue;
			foreach (array('publish', 'delete', 'edit') as $cap) {
				$user->add_cap("edit_posts");
				$user->add_cap("{$cap}_stm_lms_posts");
			}
		}
	}

}

add_action('init', 'stm_lms_add_theme_caps');