<div class="row">

	<div class="col-md-6">
		<div class="form-group">
			<label class="heading_font"><?php esc_html_e('Name', 'masterstudy-lms-learning-management-system'); ?></label>
			<input v-model="data.meta.first_name"
				   class="form-control"
				   placeholder="<?php esc_html_e('Enter your name', 'masterstudy-lms-learning-management-system') ?>"/>
		</div>
	</div>

	<div class="col-md-6">
		<div class="form-group">
			<label class="heading_font"><?php esc_html_e('Surname', 'masterstudy-lms-learning-management-system'); ?></label>
			<input v-model="data.meta.last_name"
				   class="form-control"
				   placeholder="<?php esc_html_e('Enter your last name', 'masterstudy-lms-learning-management-system') ?>"/>
		</div>
	</div>

</div>