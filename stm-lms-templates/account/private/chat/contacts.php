<?php
/**
 * @var $current_user
 */
$c = stm_lms_get_user_conversations($current_user['id']);

?>

<div class="stm_lms_chat__conversations" v-if="conversations.length">
	<div class="stm_lms_chat__conversation"
         v-for="(conversation_loop, index) in conversations"
         v-bind:class="{'active' : conversation == index}"
         @click="changeChat(index)">
		<div class="stm_lms_chat__conversation__image" v-html="conversation_loop['companion']['avatar']"></div>
		<div class="stm_lms_chat__conversation__meta">
			<div class="stm_lms_chat__conversation__title">
				<h5 v-html="conversation_loop['companion']['login']"></h5>
			</div>
			<div class="stm_lms_chat__conversation__date" v-html="conversation_loop['conversation_info']['ago']"></div>
		</div>

        <div class="stm_lms_chat__conversation__messages_num" v-bind:class="{'has_new' : conversation_loop['conversation_info']['new_messages'] != 0}">
            {{ conversation_loop['conversation_info']['messages_number'] }}
        </div>

	</div>
</div>

<div class="stm_lms_chat__conversations" v-else>
    <?php esc_html_e('No messages yet.', 'masterstudy-lms-learning-management-system'); ?>
</div>
