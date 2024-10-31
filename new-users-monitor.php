<?php
/*
Plugin Name: New Users Monitor
Plugin URI: wpgear.xyz/new-users-monitor
Description: Ext Security. Automatic scanning of the Users list, and detect unauthorized addition to the DB. Informs immediately Admin by email.
Version: 3.15
Text Domain: new_users_monitor
Author: WPGear
Author URI: http://wpgear.xyz
License: GPLv2
*/
	$NUM_plugin_url = plugin_dir_url( __FILE__); // со слэшем на конце
	
	$NUM_Setup_AdminOnly 	= get_option('num_option_adminonly', 1);		// Админка не для всех.
	$NUM_Dashboard_NewUsers = get_option('num_dashboard_newusers', 10 );	// Количество новых пользователей в виджете консоли.	
	$NUM_Scan_NewUsers		= get_option('num_scan_newusers', 1 );			// Период проверки обнаружения новых Пользователей в Часах.
	$NUM_FirstRun			= get_option('num_first_run', 0 );				// Тригер первого запуска. Чтобы не считать имеющихся Пользователей как новых.
	$NUM_Disable_Login		= get_option('num_disable_login', 1 );			// Запрещаем Вход, если Пользователь не подтвержден.
	
	$NUM_Authentication_Msg = __('Authentication Impossible.', 'new_users_monitor');
	
	__('Ext Security. Automatic scanning of the Users list, and detect unauthorized addition to the DB. Informs immediately Admin by email.', 'new_users_monitor');
	
	/* Admin Console - Styles.
	----------------------------------------------------------------- */	
	function NUM_admin_style ($hook) {
		$screen = get_current_screen();
		$screen_base = $screen->base;

		if ($screen_base == 'dashboard' || $screen_base == 'new-users-monitor/options') {
			global $NUM_plugin_url;			
		
			wp_enqueue_style ('num_admin-style', $NUM_plugin_url .'admin-style.css');
			wp_enqueue_style ('font-awesome_4.7', "https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
		}
	}
	add_action ('admin_enqueue_scripts', 'NUM_admin_style' );
	
	/* Create plugin SubMenu
	----------------------------------------------------------------- */	
	add_action('admin_menu', 'num_create_menu');
	function num_create_menu() {
		add_users_page(
			__('NUM Options', 'new_users_monitor'),
			__('New Users Monitor', 'new_users_monitor'),
			'edit_dashboard',
			plugin_dir_path(__FILE__) .'options.php',
			''
		);
	}
	
	/* Create New-Users DashboardWidget
	----------------------------------------------------------------- */	
	add_action('wp_dashboard_setup', 'NUM_Dashboard_Widgets_NewUsers');
	function NUM_Dashboard_Widgets_NewUsers() {
		if (current_user_can('edit_dashboard')) {
			global $wp_meta_boxes;
			
			wp_add_dashboard_widget('num_newuser_widget', 'New Users Monitor', 'NUM_Dashboard_NewUsers');			
		}
	}

	/* New-Users DashboardWidget
	----------------------------------------------------------------- */	
	function NUM_Dashboard_NewUsers() {
		global $wpdb;
		global $NUM_Dashboard_NewUsers;
		
		$num_users_table = $wpdb->prefix .'users';

		$Query = "
			SELECT * FROM $num_users_table 
			WHERE user_status = 0 
			ORDER BY ID DESC LIMIT %d";
			
		$users = $wpdb->get_results ($wpdb->prepare ($Query, $NUM_Dashboard_NewUsers));

		?>
		<table style="width: 100%">
			<tbody style="text-align: left;">
				<th><h3>Date reg.</h3></th>
				<th><h3>Login</h3></th>
				<th><h3>Email</h3></th>
				<th><h3>Role</h3></th>
				<?php 
				
				foreach ($users as $user) {
					$User_ID 	= $user->ID;
					$reg_date 	= $user->user_registered;
					$nicename	= $user->user_nicename;
					$user_email	= $user->user_email;
					
					$user_info 	= get_userdata($User_ID); 
					$roles		= $user_info->roles;					
					
					// Проверка подтверждения Нового Пользователя.
					$meta_key = 'num_confirm';
					$NUM_Confirm = get_user_meta( $User_ID, $meta_key, true );
					
					if ($NUM_Confirm == '') {
						// Если у Пользователя еще нет метаполей (Пользователь появился до следующего запуска сканирования), то формируем поле со значением ' '
						$meta_value = ' ';
						update_user_meta( $User_ID, $meta_key, $meta_value );						
					}
					?>
					<tr <?php if ($NUM_Confirm !== '1') {echo 'style="color: red; cursor: alias;" title="' .__('Unconfirmed User!', 'new_users_monitor') .'"';} ?>>
						<td>
							<?php  echo date('Y-m-d H:i', strtotime($reg_date));?>
						</td>
						
						<td>
							<a href="<?php echo get_edit_user_link($User_ID); ?>"><?php echo $nicename; ?></a>
						</td>
						
						<td>
							<?php echo $user_email; ?>
						</td>
						
						<td>
							<?php echo  implode(', ', $roles)?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		
		<script>
			var NUM_Widget = document.getElementById("num_newuser_widget");
			var NUM_Widget_Header = NUM_Widget.getElementsByTagName("h2")[0];
			
			var NUM_Widget_Title = "<?php echo __('Click to open Setup-Option Page!', 'new_users_monitor'); ?>";			
			var NUM_Widget_LinkCaption = "<?php echo __('New Users Monitor', 'new_users_monitor'); ?>";
			
			NUM_Widget_Header.innerHTML = '<span title="' + NUM_Widget_Title + '"><a href="/wp-admin/users.php?page=new-users-monitor/options.php" class="ulm_dashboard_widget_header">' + NUM_Widget_LinkCaption + '</a></span>';
		</script>		
	<?php }	
	
	/* User Profile Page. Add CheckBox option.
	----------------------------------------------------------------- */	
	add_action('edit_user_profile', 'NUM_show_extra_profile_fields');
	function NUM_show_extra_profile_fields ($user) {
		$User_ID = $user->ID;
		
		$meta_key = 'num_confirm';
		$meta_value = true;
		
		$NUM_Confirm = get_user_meta ($User_ID, $meta_key, true);
		
		if ($NUM_Confirm !== '1') {
			$meta_value = false;
		} ?>
		<table class="form-table">
			<tbody>
				<tr id="box_bso_confirm">
					<th <?php if($meta_value == false) {echo 'style="color: red;"';} ?>><?php echo __('Confirmation (new User)', 'new_users_monitor'); ?></th>
					<td>
						<label for="num_confirm">
						<input name="num_confirm" type="checkbox" id="num_confirm" <?php if($meta_value) {echo "checked";} ?>> <?php echo __('Confirm the credentials of this User', 'new_users_monitor'); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}	
	
	/* User Profile Page. Save CheckBox option.
	----------------------------------------------------------------- */	
	add_action( 'edit_user_profile_update', 'NUM_save_extra_profile_fields' );
	function NUM_save_extra_profile_fields($User_ID) {
		if (!current_user_can('edit_user', $User_ID)) {
			return false;
		}

		$meta_key = 'num_confirm';
		$meta_value = isset($_POST['num_confirm']) ? '1' : '0';
		
		$NUM_Confirm_Last = get_user_meta ($User_ID, $meta_key, true);
		
		if ($NUM_Confirm_Last != $meta_value) {
			// Надо обновить
			update_user_meta ($User_ID, $meta_key, $meta_value);
			
			// Делаем отметку в Журнале (как изменение Подтверждения Полномочий).
			// ...
		}
	}	
	
	// Шедуллер. Клиринг при деактивации.
	register_deactivation_hook( __FILE__, 'num_deactivation');
	function num_deactivation() {
		wp_clear_scheduled_hook('num_scan_newusers');
	}	
	
	// Шедуллер. Регистрируем интервал/
	add_filter( 'cron_schedules', 'cron_add_num_interval' );
	function cron_add_num_interval( $schedules ) {
		
		// Сканирование обнаружения Новых Пользователей.
		global $NUM_Scan_NewUsers;
		$schedules['num_scan_newusers_interval'] = array(
			'interval' => 3600 * $NUM_Scan_NewUsers,
			'display'  => "Every $NUM_Scan_NewUsers H"
		);		
		
		return $schedules;
	}

	// Шедуллер. Инициализация.
	register_activation_hook( __FILE__, 'num_activation' );	
	function num_activation() {
		// удалим на всякий случай все такие же задачи cron, чтобы добавить новые с "чистого листа"
		wp_clear_scheduled_hook('num_scan_newusers');

		// Сканирование обнаружения Новых Пользователей.
		wp_schedule_event(time(), 'num_scan_newusers_interval', 'num_scan_newusers');
	}	
	
	// Шедуллер. Сканирование обнаружения Новых Пользователей.
	add_action('num_scan_newusers', 'do_num_scan_newusers');
	if(!function_exists('do_num_scan_newusers')){
		function do_num_scan_newusers() {
			global $wpdb, $NUM_FirstRun;
			global $NUM_Dashboard_NewUsers, $NUM_Scan_NewUsers;

			$num_users_table 	= $wpdb->prefix .'users';
			$num_usermeta_table = $wpdb->prefix .'usermeta';

			$Query = "SELECT * FROM $num_users_table WHERE %d";
			
			$Users = $wpdb->get_results ($wpdb->prepare ($Query, 1));
			
			$meta_key = 'num_confirm';			
			
			if ($NUM_FirstRun == 1) {
				// Рабочий процесс
				$Site_title = get_bloginfo('name');
				$subject = "$Site_title | __('New Users Monitor. Attention! A new User has been detected.', 'new_users_monitor')";				

				$admin_email = get_option('admin_email');
				$from = $admin_email;
				
				$headers[] = "From: New Users Monitor <$from>";
				$headers[] = "Content-Type: text/html";
				$headers[] = "charset=UTF-8";
				
				$to = $admin_email;				
				
				foreach ($Users as $User) {
					$User_ID 	= $User->ID;
					$User_Login = $User->user_login;
					
					$NUM_Confirm = get_user_meta ($User_ID, $meta_key, true);
					if ($NUM_Confirm == '' || $NUM_Confirm == ' ') {
						// Новый неподтвержденный Пользователь. Необходимо уведомить Админа.
						$message = "Attention!\r\n";
						$message .= "In the DB ('users' table), a new record was found. \r\n";
						$message .= "ID: $User_ID\r\n";
						$message .= "Login: $User_Login\r\n";				
						
						// формируем HTML контент вместо Text, т.к почему-то нарушается форматирование текста. Переводы строки не работают ((
						$message = wpautop($message);
						wp_mail($to, $subject, $message, $headers);					
						
						
						// Делаем метку, что этот Пользователь уже обнаружен. 
						$meta_value = '0';
						update_user_meta( $User_ID, $meta_key, $meta_value );
						
						
						// Делаем отметку в Журнале.
						// ...
					}		
				}
			} else {
				// Первый запуск.
				$NUM_FirstRun = 1;			
				
				foreach ($Users as $User) {
					$User_ID 	= $User->ID;
					$User_Login = $User->user_login;
					
					// Делаем метку, что этот Пользователь уже обнаружен и Подтвержден. 
					$meta_value = '1';
					update_user_meta ($User_ID, $meta_key, $meta_value);
				}			

				update_option('num_dashboard_newusers', $NUM_Dashboard_NewUsers);
				update_option('num_scan_newusers', $NUM_Scan_NewUsers);				
				update_option('num_first_run', '1');
			}
		}		
	}	
	
	// Users. Добавляем новыеколонки.
	add_filter ('manage_users_columns', 'do_num_columns');
	function do_num_columns ($column) {
		$column['confirm'] = 'Confirm';

		return $column;
	}
	
	// Users. Формируем новые колонки.
	add_filter ('manage_users_custom_column', 'do_num_user_column', 10, 3);
	function do_num_user_column ($output, $column_name, $user_id) {
		// Confirm
		if ($column_name == 'confirm') {
			$meta_key = 'num_confirm';
			$NUM_Confirm = get_user_meta ($user_id, $meta_key, true);
			
			$Field_Style = '';
			$Field_Title = '';
			$Field_Content = 'ON';				
			
			if ($NUM_Confirm != 1) {
				$Field_Style = 'color: red;';
				$Field_Title = __('User Profile - No Confirm!', 'new_users_monitor');
				
				$Field_Content = 'OFF';
			} 
			
			$output = "<span style='$Field_Style' title='$Field_Title'>$Field_Content</span>";
		}
		
		return $output;
	}

	// Users. Делаем новые колонки сортируемыми.
	add_filter ('manage_users_sortable_columns', 'do_num_user_column_sortable');
	function do_num_user_column_sortable ($sortable_columns) {
		$sortable_columns['confirm'] 	= 'confirm';
		
		return $sortable_columns;
	}

	// Users. Делаем сортировку новых колонок.
	add_filter ('pre_user_query', 'do_num_user_column_orderby');
	function do_num_user_column_orderby ($user_query) {
		global $current_screen;
		
		$Screen_ID = isset ($current_screen->id) ? $current_screen->id : null;
		
		if ($Screen_ID != 'users') return;
		
		global $wpdb;
		
		$users_table 	= $wpdb->prefix .'users';
		$usermeta_table = $wpdb->prefix .'usermeta';
		
		$vars = $user_query->query_vars;	

		// "Confirm"
		if ($vars['orderby'] == 'confirm') {
			$user_query->query_orderby = " ORDER BY $usermeta_table.meta_value ". $vars['order'];
			$user_query->query_from = " FROM $users_table INNER JOIN $usermeta_table ON ($users_table.ID = $usermeta_table.user_id)";
			$user_query->query_where = " WHERE $usermeta_table.meta_key = 'num_confirm'";
		}

		return $user_query;
	}
	
	/* Запрещаем Вход, если Пользователь не подтвержден.
	----------------------------------------------------------------- */	
	function NUM_wp_authenticate_user($user) {
		global $NUM_Disable_Login, $NUM_Authentication_Msg;
		
		if ($NUM_Disable_Login) {
			$User_ID = $user -> ID;
			
			if ($User_ID) {			
				$is_User_Confirmed = get_user_meta ($User_ID, 'num_confirm', true);	
				
				if (!$is_User_Confirmed) {						
					$error = new WP_Error ('no_confirm_user', $NUM_Authentication_Msg);				
					return $error;
				}
			}		
		}

		return $user;	
	}
	add_filter('wp_authenticate_user', 'NUM_wp_authenticate_user', 1);	