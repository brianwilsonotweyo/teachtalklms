<div id="stm-lms-become-instructor" class="stm-lms-become-instructor">

	<div class="stm-lms-login__top">
		<h3><?php esc_html_e('Become Instructor', 'masterstudy-lms-learning-management-system'); ?></h3>
	</div>

	<div class="stm_lms_bi_wrapper">

		<div class="form-group" v-bind:class="{'error' : !degree_filled }">
			<label class="heading_font"><?php esc_html_e('Degree', 'masterstudy-lms-learning-management-system'); ?></label>
			<input class="form-control"
				   type="text"
				   name="degree"
				   v-model="degree"
				   placeholder="<?php esc_html_e('Enter degree', 'masterstudy-lms-learning-management-system'); ?>"/>
		</div>

		<div class="form-group" v-bind:class="{'error' : !expertize_filled }">
			<label class="heading_font"><?php esc_html_e('Expertise', 'masterstudy-lms-learning-management-system'); ?></label>
			<input class="form-control"
				   type="text"
				   name="expertize"
				   v-model="expertize"
				   placeholder="<?php esc_html_e('Enter Expertise', 'masterstudy-lms-learning-management-system'); ?>"/>
		</div>


		<a href="#"
		   class="btn btn-default"
		   v-bind:class="{'loading': loading}"
		   @click.prevent="send()">
			<span><?php esc_html_e('Send Application', 'masterstudy-lms-learning-management-system'); ?></span>
		</a>

	</div>

	<transition name="slide-fade">
		<div class="stm-lms-message" v-bind:class="status" v-if="message">
			{{ message }}
		</div>
	</transition>

</div>