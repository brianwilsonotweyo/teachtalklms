<div id="stm-lms-enterprise" class="stm-lms-enterprise">

	<div class="stm-lms-login__top">
		<h3><?php esc_html_e('Become Instructor', 'masterstudy-lms-learning-management-system'); ?></h3>
	</div>

	<div class="stm_lms_enterprise_wrapper">

		<div class="form-group">
			<label class="heading_font"><?php esc_html_e('Name', 'masterstudy-lms-learning-management-system'); ?></label>
			<input class="form-control"
				   type="text"
				   name="name"
				   v-model="name"
				   placeholder="<?php esc_html_e('Enter your name', 'masterstudy-lms-learning-management-system'); ?>"/>
		</div>

		<div class="form-group">
            <label class="heading_font"><?php esc_html_e('E-mail', 'masterstudy-lms-learning-management-system'); ?></label>
            <input class="form-control"
                   type="text"
                   name="email"
                   v-model="email"
                   placeholder="<?php esc_html_e('Enter Your Email', 'masterstudy-lms-learning-management-system'); ?>"/>
        </div>

        <div class="form-group">
            <label class="heading_font"><?php esc_html_e('Message', 'masterstudy-lms-learning-management-system'); ?></label>
            <textarea class="form-control"
                      type="text"
                      name="text"
                      v-model="text"
                      placeholder="<?php esc_html_e('Enter Your Message', 'masterstudy-lms-learning-management-system'); ?>"></textarea>
        </div>


		<a href="#"
		   class="btn btn-default"
		   v-bind:class="{'loading': loading}"
		   @click.prevent="send()">
			<span><?php esc_html_e('Send Enquiry', 'masterstudy-lms-learning-management-system'); ?></span>
		</a>

	</div>

	<transition name="slide-fade">
		<div class="stm-lms-message" v-bind:class="status" v-if="message">
			{{ message }}
		</div>
	</transition>

</div>