<?php
namespace stmLms\Classes\Models\Admin;

use stmLms\Classes\Models\StmOrderItems;
use WP_User_Query;
use \WP_List_Table;
use stmLms\Classes\Vendor\Query;
use stmLms\Classes\Models\StmOrder;

class StmStatisticsListTable extends WP_List_Table{

	public $total_price = 0;

	public function __construct(){
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
	 	parent::__construct(array(
	 		'singular' => __( 'Order', 'masterstudy-lms-learning-management-system' ),
	 		'plural'   =>  __( 'Orders', 'masterstudy-lms-learning-management-system' ),
	 		'ajax'     => false,
	 	));
	 	$this->bulk_action_handler();
	 	$this->prepare_items();
		add_action( 'wp_print_scripts', [ __CLASS__, '_list_table_css' ] );
	}

	/**
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function prepare_items(){
		global $wpdb;
		$this->_column_headers = $this->get_column_info();
		$per_page     = $this->get_items_per_page( 'stm_lms_statistics_per_page', 10);
		$current_page = $this->get_pagenum();
		$this->items = $this->getList( $per_page, $current_page );
		$total_items  = sizeof($this->getList( 99999999, 1 ));
		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		]);
	}

	/**
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return array|int|null|object
	 */
	public  function getList( $per_page = 5, $page_number = 0 ) {
		global $wpdb;
		$prefix = $wpdb->prefix;
		$query = StmOrder::query()
					 ->select(" _order.*, meta.* ")
					 ->asTable("_order")
					 ->join(" left join `".$prefix."stm_lms_order_items` as lms_order_items on ( lms_order_items.`order_id` = _order.ID )
				                   left join `".$prefix."posts` as course on  (course.ID = lms_order_items.`object_id`) ")
					 ->where_in("_order.post_type", ["stm-orders", "shop_order"]);

		if(isset($_GET['filter']['id'])  AND !empty($_GET['filter']['id']) ) {
			$query->where('_order.ID', $_GET['filter']['id']);
		}

		if( isset($_GET['filter']['created_date_from']) AND !empty(trim($_GET['filter']['created_date_from'])) AND isset($_GET['filter']['created_date_to']) AND !empty(trim($_GET['filter']['created_date_to']))) {
			$query->where_raw('
				DATE(_order.post_date) >= "'.date( "Y-m-d", strtotime($_GET['filter']['created_date_from']) ).'" AND
				DATE(_order.post_date) <= "'.date( "Y-m-d", strtotime($_GET['filter']['created_date_to']) ).'"
			');
		}

		if(isset($_GET['filter']['total_price']) AND !empty($_GET['filter']['total_price']) ) {
			$query->where_raw(' ( meta.meta_key = "_order_total" AND meta.meta_value = "'.$_GET['filter']['total_price'].'" ) ');
		}

		if(isset($_GET['filter']['status']) AND !empty($_GET['filter']['status']) ) {
			$query->where_raw('
			(
				( meta.meta_key = "status" AND meta.meta_value = "'.$_GET['filter']['status'].'" ) OR
				( _order.post_status = "'.$_GET['filter']['status'].'" )
			) 
			');
		}

		if(isset($_GET['filter']['user']) AND !empty($_GET['filter']['user']) ) {
			$ids = [$_GET['filter']['user']];
			if(!empty($ids)){
				$query->where_raw('
					(
						(meta.meta_key = "user_id" AND meta.meta_value in ('.implode(",", $ids).')) OR
						(meta.meta_key = "_customer_user" AND meta.meta_value in ('.implode(",", $ids).'))
					)
				');
			}
		}

		if(isset($_GET['filter']['post_author']) AND !empty($_GET['filter']['post_author']) ) {
			$query->where("course.`post_author`", (int) $_GET['filter']['post_author']);
		}

		$query_total_price = clone $query;
		$query_total_price->select(" SUM( lms_order_items.`price` * lms_order_items.`quantity`) as total_price ");
		$this->total_price = $query_total_price->findOne()->total_price;

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$query->sort_by(esc_sql( $_REQUEST['orderby'] ))
			      ->order(! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC');
		}else{
			$query->sort_by("ID")->order(" DESC ");
		}

		$query->join(" left join ".$prefix."postmeta as meta on (meta.post_id = _order.ID)")
		      ->group_by("_order.ID")
			  ->limit($per_page)
			  ->offset(($page_number > 1) ? (($page_number - 1) * $per_page ) : 0);

		return $query->find(false, Query::OUTPUT_OBJECT);
	}

	/**
	 * @return array
	 */
	public function get_columns(){
		return array(
			'ID'        => 'ID',
			'type'      => __("Order type", "masterstudy-lms-learning-management-system"),
			'items'     => __("Items", "masterstudy-lms-learning-management-system"),
			'user'      => __("User", "masterstudy-lms-learning-management-system"),
			'status'    => __("Status", "masterstudy-lms-learning-management-system"),
			'price'     => __("Price", "masterstudy-lms-learning-management-system"),
			'post_date' => __("Created date", "masterstudy-lms-learning-management-system"),
		);
	}

	/**
	 * @return array
	 */
	public function get_sortable_columns(){
		return array(
			'ID' => array( 'ID', 'desc' ),
			'post_date' => array( 'post_date', 'desc' ),
		);
	}

	protected function get_bulk_actions() {
		return;
		return array(
			'delete' => 'Delete',
		);
	}

	/**
	 * @param string $which
	 */
	public function extra_tablenav( $which ){
		if($which == "bottom")
			return;
		$id = (isset($_GET['filter']['id'])) ? $_GET['filter']['id'] : null;
		$status = (isset($_GET['filter']['status'])) ? $_GET['filter']['status'] : null;
		$total_price = (isset($_GET['filter']['total_price'])) ? $_GET['filter']['total_price'] : null;
		$created_date_from = (isset($_GET['filter']['created_date_from'])) ? $_GET['filter']['created_date_from'] : null;
		$created_date_to = (isset($_GET['filter']['created_date_to'])) ? $_GET['filter']['created_date_to'] : null;

		$author  = [];
		$user    = [];
		$_author = __("Search by Author", "masterstudy-lms-learning-management-system");
		$_user   = __("Search by User", "masterstudy-lms-learning-management-system");

		$extra_tablenav = '
						<hr>
						<div class="alignleft actions">
							<input class="form-control" type="text" style="width: 100px" name="filter[id]" value="'.$id.'" placeholder="'.__("Search by ID", "masterstudy-lms-learning-management-system").'">
							<div id="stm-user-search" class="stm-user-search--app"> 
								<stm-user-search key="search_by_user" class="stm-user-search"  inline-template :user="user" v-on:stm-user-search="selectUser">
									<div>
										<v-select label="name"  v-model="user" :filterable="false" :options="options" @search="onSearch"placeholder="'.$_user.'">
											<template slot="no-options">
												'.__("Type to name or email for search user", "").'
											</template>
											<template slot="option" slot-scope="option">
												<div class="d-center">
													{{option.id}} {{option.name}} {{option.email}}
												</div>
											</template>
											<template slot="selected-option" scope="option">
												<div class="selected d-center">
													{{option.id}} {{option.name}} {{option.email}}
												</div>
											</template>
										</v-select>
									</div>
								</stm-user-search>	
								<input v-if="user" name="filter[user]" type="hidden" v-model="user.id">
							</div>
							<div id="stm-author-search" class="stm-user-search--app"> 
								<stm-user-search key="search_by_author"  class="stm-user-search"  inline-template :user="user" v-on:stm-user-search="selectUser">
									<div>
										<v-select label="name"  v-model="user" :filterable="false" :options="options" @search="onSearch"placeholder="'.$_author.'">
											<template slot="no-options">
												'.__("Type to name or email for search user", "").'
											</template>
											<template slot="option" slot-scope="option">
												<div class="d-center">
													{{option.id}} {{option.name}} {{option.email}}
												</div>
											</template>
											<template slot="selected-option" scope="option">
												<div class="selected d-center">
													{{option.id}} {{option.name}} {{option.email}}
												</div>
											</template>
										</v-select>
									</div>
								</stm-user-search>	
								<input v-if="user" name="filter[post_author]" type="hidden" v-model="user.id">
							</div>
							<input class="form-control" type="text" style="width: 130px" name="filter[status]" value="'.$status.'" placeholder="'.esc_html__("Search by Status", "masterstudy-lms-learning-management-system").'">
							<input class="form-control" type="text" name="filter[total_price]" value="'.$total_price.'" placeholder="'.esc_html__("Search by Total price", "masterstudy-lms-learning-management-system").'">
							<span class="form-control"> from :</span> <input class="form-control" style="width: 140px" type="date" name="filter[created_date_from]" value="'.$created_date_from .'" class="stm_plan_user_filter_date" />
							<span class="form-control"> to :</span> <input class="form-control" style="width: 140px" type="date" name="filter[created_date_to]" value="'.$created_date_to .'" class="stm_plan_user_filter_date" />
							
							<button class="button">'.esc_html__('Apply', "masterstudy-lms-learning-management-system").'</button>
						</div>
						<div style="clear: both"></div>
						<hr>
						';
		echo stm_lms_filtered_output($extra_tablenav);
	}

	public static function _list_table_css(){
		?>
		<style>
			.column-ID{ width:80px; }
			.column-items{ width:350px; }
			.tablenav .actions{ overflow: visible; padding: 0 }
			.stm-user-search--app{
				float: left;
			}
			.stm-user-search--app .stm-user-search{
				width: 200px;
				margin: 0px 1px;
				float: left;
			}
			.form-control{
				box-shadow: none !important;
				padding: 6px 5px;
				margin-top: 0px;
				height: 32px;
				float: left;
			}
		</style>
		<script>

			var user = <?php
				if(isset($_GET['filter']['user'])) {
					$user = get_userdata($_GET['filter']['user']);
					echo json_encode(array(
						'id' => $user->data->ID,
						'name' => $user->data->display_name,
						'email' => $user->data->user_email
					));
				}else
					echo "null";
				?>

			var author = <?php
				if(isset($_GET['filter']['post_author'])) {
					$post_author = get_userdata($_GET['filter']['post_author']);
					echo json_encode(array(
						'id' => $post_author->data->ID,
						'name' => $post_author->data->display_name,
						'email' => $post_author->data->user_email
					));
				}else
					echo "null";
				?>

			document.addEventListener('DOMContentLoaded', function() {
				new Vue({
					el:'#stm-user-search',
					data:{
						user: null
					},
					created(){
						if(user){
							this.user = user;
						}
					},
					methods:{
						selectUser:function(user) {
							this.user = user;
						}
					}
				})

				new Vue({
					el:'#stm-author-search',
					data:{
						user:null
					},
					created(){
						if(author){
							this.user = author;
						}
					},
					methods:{
						selectUser:function(user) {
							this.user = user;
						}
					}
				})
			});


		</script>


		<?php
	}

	/**
	 * @param object $item
	 * @param string $colname
	 *
	 * @return null|string|void
	 */
	public function column_default( $item, $colname ){
		global $wpdb;
		$user   = null;
		$status = null;
		$price  = null;
		$type   = null;
		$meta   = get_post_meta($item->ID);
		$items  = StmOrderItems::query()->where('order_id', $item->ID)->find();

		if($item->post_type == "stm-orders"){
			$type = "Lms";
			$user = get_userdata($meta['user_id'][0]);
			$status = $meta['status'][0];
			$_order_currency = (isset($meta['_order_currency'])) ? $meta['_order_currency'][0] : null;
			$price = (isset($meta['_order_total']) AND isset($meta['_order_total'][0])) ? $meta['_order_total'][0].' '.$_order_currency : 0;
		}

		if($item->post_type == "shop_order"){
			$type = "WooCommerce";
			$user   = get_userdata($meta['_customer_user'][0]);
			$price  = $meta['_order_total'][0].' '.$meta['_order_currency'][0];
			$status = $item->post_status;
		}
		switch ($colname){
			case "ID":
				return "#".$item->$colname;
				break;
			case "items":
				$content = "";
				foreach ( $items as $item ) {
					$item_post = $item->get_items_posts();
					if(!$item_post)
						continue;
					$author    = $item->get_items_author();

					$payout = ($item->transaction) ? "Yes" : "No";
					$content .= "<strong>". __( 'Course', 'masterstudy-lms-learning-management-system' )." </strong>: ".$item_post->post_title." <br> 
							     <strong>". __( 'Quantity', 'masterstudy-lms-learning-management-system' )." </strong>: ".$item->quantity." <br> 
							     <strong>". __( 'Price', 'masterstudy-lms-learning-management-system' )." </strong>: ".$item->price." <br> 
							     <strong>". __( 'Author', 'masterstudy-lms-learning-management-system' )." </strong>: ".$author->ID." ".$author->user_email." ".$author->user_firstname." <br>
							     <strong>". __( 'Payout', 'masterstudy-lms-learning-management-system' )." </strong>: ".$payout;
					$content.="<hr>";
				}
				return $content;
				break;
			case "user":
				if($user)
					return "(".$user->ID.")" . $user->user_firstname." ".$user->user_lastname. " <strong> (".$user->user_email.") </strong>";
				else
					return "Not found";
				break;
			case "status":
				return $status;
				break;
			case "type":
				return $type;
				break;
			case "price":
				return $price;
				break;
			case "post_date":
				return date_i18n( get_option( 'date_format' ), strtotime( $item->$colname ) ).' <br> '.date_i18n( get_option( 'time_format' ), strtotime( $item->$colname ) );
				break;
			default:
				return $item->$colname;
				break;
		}
	}

	private function bulk_action_handler(){
		if( empty($_POST['licids']) || empty($_POST['_wpnonce']) ) return;
		if ( ! $action = $this->current_action() ) return;
		if( ! wp_verify_nonce( $_POST['_wpnonce'], 'bulk-' . $this->_args['plural'] ) )
			wp_die('nonce error');
	}

}


