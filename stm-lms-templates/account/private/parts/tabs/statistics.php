<?php
stm_lms_register_style('datepicker');
stm_lms_register_script('moment.min');
stm_lms_register_script('chart.min');
stm_lms_register_script('vue-chartjs.min');

$user = new \stmLms\Classes\Models\StmUser(get_current_user_id());

$data = ["author_id" => $user->ID];
$data["paypal_email"] = get_user_meta($user->ID, "stm_lms_paypal_email", true);

$data["currency_symbol"] = STM_LMS_Options::get_option('currency_symbol', '$');

$user_courses = $user->get_courses();
$date_end = new DateTime();
$date_start = new DateTime();
$date_start->modify('first day of this month')->setTime(0,0,0);
date_sub($date_start, date_interval_create_from_date_string('5 months'));


$date_start_n =  clone $date_start;
$courses_earnings_date = [];
$labels_earnings = [];
$datasets_earnings = [];

for ($i = 0; $i < 6; $i++) {
	$labels_earnings[] = $date_start_n->format("F");
	$courses_earnings_date[] = $date_start_n->format("d-m-Y");
	date_add($date_start_n, date_interval_create_from_date_string('1 months'));
}

foreach ($user_courses as $k => $user_course){
	$courses = \stmLms\Classes\Models\StmStatistics::get_course_statisticas($date_start->format("Y-m-d"), $date_end->format("Y-m-d"), $user->ID, $user_course->ID);
	$datasets_data = [];
	foreach ($courses as $course) {
		foreach ($courses_earnings_date as $_date){
			$_date  = new DateTime($_date);
			$m = (int) $_date->format("m");
			$_date  = $_date->format("m-Y");
			$course_date  = new DateTime($course['date']);

			if(!isset($datasets_data[$m]))
				$datasets_data[$m] = 0;

			if( $course_date->format("m-Y") ==  $_date){
				$datasets_data[$m] += $course["amount"];
			}
		}
		$datasets_earnings[$k] = [
			"label" => $course['title'],
			"backgroundColor" => $course['backgroundColor'],
			"data" => array_values($datasets_data),
		];
	}
}

$data['labels_earnings'] = $labels_earnings;
$data['datasets_earnings'] = $datasets_earnings;

$course_sales_statisticas = \stmLms\Classes\Models\StmStatistics::get_course_sales_statisticas($user->ID);
$data['sales_statisticas'] = $course_sales_statisticas;

stm_lms_register_script('account-statistics',[], false, " Vue.http.options.root = '".STM_LMS_BASE_API_URL."'; var account_statistics_data = JSON.parse('". stm_lms_convert_content(json_encode($data)) ."');");
?>
<div id="stm-account-statistics" v-bind:class="{'min-height-500': order_items.length}">
	<div class="stm-lms-user-quizzes">

		<div class="row">
			<div class="col-xs-12">
				<div v-if="paypal_email_result" class="stm-lms-message" v-bind:class="paypal_email_result.status">
					{{paypal_email_result.message}}
				</div>
			</div>

			<div class="col-xs-12 col-sm-6">
				<h3><?php esc_html_e('Statistics', 'masterstudy-lms-learning-management-system'); ?></h3>
			</div>
			<div class="col-xs-12 col-sm-6">
					<div class="col-xs-12 col-sm-8">
						<div class="form-group">
							<input v-model="paypal_email" type="text" class="form-control" placeholder="<?php esc_html_e('Paypal Email', 'masterstudy-lms-learning-management-system');?>">
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<button @click="save_email" class="btn btn-default" v-bind:class="{ loading:paypal_email_loader }" ><span><?php esc_html_e('Save', 'masterstudy-lms-learning-management-system'); ?></span></button>
					</div>
			</div>
		</div>

		<div class="row" v-if="order_items.length">
			<div class="col-xs-12 col-sm-12">
				<line-chart :labels="labels_earnings"  :datasets="datasets_earnings" chartId="line_chart_id" class="statistics-chart-class"></line-chart>
			</div>
			<div class="col-xs-12 col-sm-12">
				<line-pie :labels="labels_sales"  :datasets="datasets_sales"  chartId="pie_chart_id" class="statistics-chart-class"></line-pie>
			</div>
		</div>

	<div v-if="order_items.length" class="stm-lms-user-quiz__head heading_font">
			<div class="stm-lms-user-quiz__head_title">
				<select name="category" class="form-control disable-select" v-model="selected_course">
					<option v-for="course in courses" v-bind:value="course.id">{{course.title}}</option>
				</select>
			</div>
			<div class="stm-lms-user-quiz__head_title">
				<div class="stm-datepicker">
					<date-picker v-model="date_range"
								 range
						         lang="en"
								 confirm
								 :clearable="true"
								 input-class="form-control" width="100%"
								 format="DD/MM/YYYY"
								 @confirm="dateChanged"
								 @clear="date_range = null">
					</date-picker>
				</div>
			</div>
			<div class="stm-lms-user-quiz__head_title text-right">
				<h5 class="p-t-15"><?php esc_html_e('Total price', 'masterstudy-lms-learning-management-system'); ?> : <strong>{{total_price}}</strong></h5>
			</div>
			<div class="stm-lms-user-quiz__head_title text-right">
				<h5 class="p-t-15"><?php esc_html_e('Total count', 'masterstudy-lms-learning-management-system'); ?> : <strong>{{total}}</strong></h5>
			</div>
		</div>

	</div>
	<table class="table table-striped" v-if="order_items.length">
		<thead>
			<tr>
				<th>#</th>
				<th><?php esc_html_e('Course', 'masterstudy-lms-learning-management-system'); ?></th>
				<th><?php esc_html_e('Qty', 'masterstudy-lms-learning-management-system'); ?></th>
				<th><?php esc_html_e('Total Price', 'masterstudy-lms-learning-management-system'); ?>  ({{currency_symbol}}) </th>
				<th><?php esc_html_e('Payout', 'masterstudy-lms-learning-management-system'); ?></th>
				<th><?php esc_html_e('Created', 'masterstudy-lms-learning-management-system'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="(item, index) in order_items">
				<th scope="row">{{index + 1}}</th>

				<td>{{item.name}}</td>
				<td>{{item.quantity}}</td>
				<td>{{formatPrice(item.price * item.quantity)}}</td>
				<td>
					<span v-if="item.transaction == '1'">Yes</span>
					<span v-if="item.transaction == '0'">No</span>
				</td>
				<td>{{moment(item.date_created).format("DD/MM/YYYY")}}</td>
			</tr>
		</tbody>
	</table>

	<div v-if="order_items.length" class="text-center p-b-30">
		<button v-if="show_load_more" @click="load_more()" class="btn btn-default" v-bind:class="{loading:load_more_loading}"><span><?php esc_html_e('Load more', 'masterstudy-lms-learning-management-system'); ?></span></button>
	</div>
	<h3 class="text-center p-t-30" v-if="!order_items.length" ><?php _e("Statistics have not yet appeared.", "masterstudy-lms-learning-management-system")?></h3>
</div>


