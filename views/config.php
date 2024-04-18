<?php

//phpcs:disable VariableAnalysis
// There are "undefined" variables here because they're defined in the code that includes this file as a template.

?>
<div id="ds8googlesheetwoo-plugin-container">
	<div class="ds8googlesheetwoo-masthead">
		<div class="ds8googlesheetwoo-masthead__inside-container">
			<div class="ds8googlesheetwoo-masthead__logo-container">
				<img class="ds8googlesheetwoo-masthead__logo" src="<?php echo esc_url( plugins_url( '../_inc/img/logo-full-2x.png', __FILE__ ) ); ?>" alt="FDEstadisticas" />
			</div>
		</div>
	</div>
	<div class="ds8googlesheetwoo-lower">
		<?php if ( ! empty( $notices ) ) { ?>
			<?php foreach ( $notices as $notice ) { ?>
				<?php DS8GoogleSheetWOO::view( 'notice', $notice ); ?>
			<?php } ?>
		<?php } ?>
		<?php if ( $stat_totals && isset( $stat_totals['all'] ) && (int) $stat_totals['all']->spam > 0 ) : ?>
			<div class="ds8googlesheetwoo-card">
				<div class="ds8googlesheetwoo-section-header">
					<div class="ds8googlesheetwoo-section-header__label">
						<span><?php esc_html_e( 'Statistics' , 'ds8googlesheetwoo'); ?></span>
					</div>
					<div class="ds8googlesheetwoo-section-header__actions">
						<a href="<?php echo esc_url( DS8GoogleSheetWOO::get_page_url( 'stats' ) ); ?>">
							<?php esc_html_e( 'Detailed Stats' , 'ds8googlesheetwoo');?>
						</a>
					</div>
				</div>
				
			</div>
		<?php endif;?>

		<?php if ( $ds8googlesheetwoo_user ) : ?>
			<div class="ds8googlesheetwoo-card">
				<div class="ds8googlesheetwoo-section-header">
					<div class="ds8googlesheetwoo-section-header__label">
						<span><?php esc_html_e( 'Settings' , 'ds8googlesheetwoo'); ?></span>
					</div>
				</div>

				<div class="inside">
					<form action="<?php echo esc_url( DS8GoogleSheetWOO::get_page_url() ); ?>" method="POST">
						<table cellspacing="0" class="ds8googlesheetwoo-settings">
							<tbody>
								<?php if ( ! FDEstadisticas::predefined_api_key() ) { ?>
								<tr>
									<th class="ds8googlesheetwoo-api-key" width="10%" align="left" scope="row">
										<label for="key"><?php esc_html_e( 'API Key', 'ds8googlesheetwoo' ); ?></label>
									</th>
									<td width="5%"/>
									<td align="left">
										<span class="api-key"><input id="key" name="key" type="text" size="15" value="<?php echo esc_attr( get_option('wordpress_api_key') ); ?>" class="<?php echo esc_attr( 'regular-text code ' . $ds8googlesheetwoo_user->status ); ?>"></span>
									</td>
								</tr>
								<?php } ?>
								<?php if ( isset( $_GET['ssl_status'] ) ) { ?>
									<tr>
										<th align="left" scope="row"><?php esc_html_e( 'SSL Status', 'ds8googlesheetwoo' ); ?></th>
										<td></td>
										<td align="left">
											<p>
												<?php

												if ( ! wp_http_supports( array( 'ssl' ) ) ) {
													?><b><?php esc_html_e( 'Disabled.', 'ds8googlesheetwoo' ); ?></b> <?php esc_html_e( 'Your Web server cannot make SSL requests; contact your Web host and ask them to add support for SSL requests.', 'ds8googlesheetwoo' ); ?><?php
												}
												else {
													$ssl_disabled = get_option( 'ds8googlesheetwoo_ssl_disabled' );

													if ( $ssl_disabled ) {
														?><b><?php esc_html_e( 'Temporarily disabled.', 'ds8googlesheetwoo' ); ?></b> <?php esc_html_e( 'FDEstadisticas encountered a problem with a previous SSL request and disabled it temporarily. It will begin using SSL for requests again shortly.', 'ds8googlesheetwoo' ); ?><?php
													}
													else {
														?><b><?php esc_html_e( 'Enabled.', 'ds8googlesheetwoo' ); ?></b> <?php esc_html_e( 'All systems functional.', 'ds8googlesheetwoo' ); ?><?php
													}
												}

												?>
											</p>
										</td>
									</tr>
								<?php } ?>
								<tr>
									<th align="left" scope="row"><?php esc_html_e('Comments', 'ds8googlesheetwoo');?></th>
									<td></td>
									<td align="left">
										<p>
											<label for="ds8googlesheetwoo_show_user_comments_approved" title="<?php esc_attr_e( 'Show approved comments' , 'ds8googlesheetwoo'); ?>">
												<input
													name="ds8googlesheetwoo_show_user_comments_approved"
													id="ds8googlesheetwoo_show_user_comments_approved"
													value="1"
													type="checkbox"
													<?php
													
													// If the option isn't set, or if it's enabled ('1'), or if it was enabled a long time ago ('true'), check the checkbox.
													checked( true, ( in_array( get_option( 'ds8googlesheetwoo_show_user_comments_approved' ), array( false, '1', 'true' ), true ) ) );
													
													?>
													/>
												<?php esc_html_e( 'Show the number of approved comments beside each comment author', 'ds8googlesheetwoo' ); ?>
											</label>
										</p>
									</td>
								</tr>
								<tr>
									<th class="strictness" align="left" scope="row"><?php esc_html_e('Strictness', 'ds8googlesheetwoo'); ?></th>
									<td></td>
									<td align="left">
										<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('FDEstadisticas anti-spam strictness', 'ds8googlesheetwoo'); ?></span></legend>
										<p><label for="ds8googlesheetwoo_strictness_1"><input type="radio" name="ds8googlesheetwoo_strictness" id="ds8googlesheetwoo_strictness_1" value="1" <?php checked('1', get_option('ds8googlesheetwoo_strictness')); ?> /> <?php esc_html_e('Silently discard the worst and most pervasive spam so I never see it.', 'ds8googlesheetwoo'); ?></label></p>
										<p><label for="ds8googlesheetwoo_strictness_0"><input type="radio" name="ds8googlesheetwoo_strictness" id="ds8googlesheetwoo_strictness_0" value="0" <?php checked('0', get_option('ds8googlesheetwoo_strictness')); ?> /> <?php esc_html_e('Always put spam in the Spam folder for review.', 'ds8googlesheetwoo'); ?></label></p>
										</fieldset>
										<span class="ds8googlesheetwoo-note"><strong><?php esc_html_e('Note:', 'ds8googlesheetwoo');?></strong>
										<?php
									
										$delete_interval = max( 1, intval( apply_filters( 'ds8googlesheetwoo_delete_comment_interval', 15 ) ) );
									
										printf(
											_n(
												'Spam in the <a href="%1$s">spam folder</a> older than 1 day is deleted automatically.',
												'Spam in the <a href="%1$s">spam folder</a> older than %2$d days is deleted automatically.',
												$delete_interval,
												'ds8googlesheetwoo'
											),
											admin_url( 'edit-comments.php?comment_status=spam' ),
											$delete_interval
										);
									
										?>
									</td>
								</tr>
								<tr>
									<th class="comment-form-privacy-notice" align="left" scope="row"><?php esc_html_e('Privacy', 'ds8googlesheetwoo'); ?></th>
									<td></td>
									<td align="left">
										<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('FDEstadisticas privacy notice', 'ds8googlesheetwoo'); ?></span></legend>
										<p><label for="ds8googlesheetwoo_comment_form_privacy_notice_display"><input type="radio" name="ds8googlesheetwoo_comment_form_privacy_notice" id="ds8googlesheetwoo_comment_form_privacy_notice_display" value="display" <?php checked('display', get_option('ds8googlesheetwoo_comment_form_privacy_notice')); ?> /> <?php esc_html_e('Display a privacy notice under your comment forms.', 'ds8googlesheetwoo'); ?></label></p>
										<p><label for="ds8googlesheetwoo_comment_form_privacy_notice_hide"><input type="radio" name="ds8googlesheetwoo_comment_form_privacy_notice" id="ds8googlesheetwoo_comment_form_privacy_notice_hide" value="hide" <?php echo in_array( get_option('ds8googlesheetwoo_comment_form_privacy_notice'), array('display', 'hide') ) ? checked('hide', get_option('ds8googlesheetwoo_comment_form_privacy_notice'), false) : 'checked="checked"'; ?> /> <?php esc_html_e('Do not display privacy notice.', 'ds8googlesheetwoo'); ?></label></p>
										</fieldset>
										<span class="ds8googlesheetwoo-note"><?php esc_html_e( 'To help your site with transparency under privacy laws like the GDPR, FDEstadisticas can display a notice to your users under your comment forms. This feature is disabled by default, however, you can turn it on above.', 'ds8googlesheetwoo' );?></span>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="ds8googlesheetwoo-card-actions">
							<?php if ( ! FDEstadisticas::predefined_api_key() ) { ?>
							<div id="delete-action">
								<a class="submitdelete deletion" href="<?php echo esc_url( DS8GoogleSheetWOO::get_page_url( 'delete_key' ) ); ?>"><?php esc_html_e('Disconnect this account', 'ds8googlesheetwoo'); ?></a>
							</div>
							<?php } ?>
							<?php wp_nonce_field(DS8GoogleSheetWOO::NONCE) ?>
							<div id="publishing-action">
								<input type="hidden" name="action" value="enter-key">
								<input type="submit" name="submit" id="submit" class="ds8googlesheetwoo-button ds8googlesheetwoo-could-be-primary" value="<?php esc_attr_e('Save Changes', 'ds8googlesheetwoo');?>">
							</div>
							<div class="clear"></div>
						</div>
					</form>
				</div>
			</div>
			
			<?php if ( ! FDEstadisticas::predefined_api_key() ) { ?>
				<div class="ds8googlesheetwoo-card">
					<div class="ds8googlesheetwoo-section-header">
						<div class="ds8googlesheetwoo-section-header__label">
							<span><?php esc_html_e( 'Account' , 'ds8googlesheetwoo'); ?></span>
						</div>
					</div>
				
					<div class="inside">
						<table cellspacing="0" border="0" class="ds8googlesheetwoo-settings">
							<tbody>
								<tr>
									<th scope="row" align="left"><?php esc_html_e( 'Subscription Type' , 'ds8googlesheetwoo');?></th>
									<td width="5%"/>
									<td align="left">
										<p><?php echo esc_html( $ds8googlesheetwoo_user->account_name ); ?></p>
									</td>
								</tr>
								<tr>
									<th scope="row" align="left"><?php esc_html_e( 'Status' , 'ds8googlesheetwoo');?></th>
									<td width="5%"/>
									<td align="left">
										<p><?php 
											if ( 'cancelled' == $ds8googlesheetwoo_user->status ) :
												esc_html_e( 'Cancelled', 'ds8googlesheetwoo' ); 
											elseif ( 'suspended' == $ds8googlesheetwoo_user->status ) :
												esc_html_e( 'Suspended', 'ds8googlesheetwoo' );
											elseif ( 'missing' == $ds8googlesheetwoo_user->status ) :
												esc_html_e( 'Missing', 'ds8googlesheetwoo' ); 
											elseif ( 'no-sub' == $ds8googlesheetwoo_user->status ) :
												esc_html_e( 'No Subscription Found', 'ds8googlesheetwoo' );
											else :
												esc_html_e( 'Active', 'ds8googlesheetwoo' );  
											endif; ?></p>
									</td>
								</tr>
								<?php if ( $ds8googlesheetwoo_user->next_billing_date ) : ?>
								<tr>
									<th scope="row" align="left"><?php esc_html_e( 'Next Billing Date' , 'ds8googlesheetwoo');?></th>
									<td width="5%"/>
									<td align="left">
										<p><?php echo date( 'F j, Y', $ds8googlesheetwoo_user->next_billing_date ); ?></p>
									</td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<div class="ds8googlesheetwoo-card-actions">
							<div id="publishing-action">
								<?php FDEstadisticas::view( 'get', array( 'text' => ( $ds8googlesheetwoo_user->account_type == 'free-api-key' && $ds8googlesheetwoo_user->status == 'active' ? __( 'Upgrade' , 'ds8googlesheetwoo') : __( 'Change' , 'ds8googlesheetwoo') ), 'redirect' => 'upgrade' ) ); ?>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php endif;?>
	</div>
</div>
