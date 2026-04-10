<?php
/**
 * Development tab — Connectors.
 *
 * ROLE: template
 *
 * Shows all registered API connectors with their configuration status,
 * capabilities, and a test connection button for configured connectors.
 *
 * @package  WPSeed
 * @category Admin
 * @since    3.1.0
 */

defined( 'ABSPATH' ) || exit;

$providers  = WPSeed_API_Directory::get_all_providers();
$configured = WPSeed_API_Directory::get_configured_providers();
?>

<div class="wpseed-admin-wrap">

	<div class="wpseed-arch-section">
		<h3><?php esc_html_e( 'Registered Connectors', 'wpseed' ); ?></h3>
		<p><?php esc_html_e( 'API connectors registered via the API Directory. Each implements the Connector Interface.', 'wpseed' ); ?></p>

		<?php if ( empty( $providers ) ) : ?>
			<p><em><?php esc_html_e( 'No connectors registered.', 'wpseed' ); ?></em></p>
		<?php else : ?>

			<div class="wpseed-components-grid">
				<?php foreach ( $providers as $provider_id => $provider ) :
					$is_configured = isset( $configured[ $provider_id ] );
					$status_class  = $is_configured ? 'wpseed-status-success' : 'wpseed-status-warning';
					$status_label  = $is_configured
						? __( 'Configured', 'wpseed' )
						: __( 'Not Configured', 'wpseed' );
				?>
					<div class="wpseed-card">
						<div class="wpseed-card-header" style="display: flex; justify-content: space-between; align-items: center;">
							<strong>
								<span class="dashicons <?php echo esc_attr( $provider['icon'] ?? 'dashicons-admin-generic' ); ?>" style="margin-right: 4px;"></span>
								<?php echo esc_html( $provider['name'] ?: $provider_id ); ?>
							</strong>
							<span class="wpseed-badge <?php echo esc_attr( $status_class ); ?>">
								<?php echo esc_html( $status_label ); ?>
							</span>
						</div>
						<div class="wpseed-card-body">
							<?php if ( ! empty( $provider['description'] ) ) : ?>
								<p class="description"><?php echo esc_html( $provider['description'] ); ?></p>
							<?php endif; ?>

							<table class="wpseed-table wpseed-table-sm" style="margin-top: 8px;">
								<tr>
									<th scope="row"><?php esc_html_e( 'Provider ID', 'wpseed' ); ?></th>
									<td><code><?php echo esc_html( $provider_id ); ?></code></td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Auth Type', 'wpseed' ); ?></th>
									<td><?php echo esc_html( $provider['auth_type'] ?? 'bearer' ); ?></td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Class', 'wpseed' ); ?></th>
									<td><code><?php echo esc_html( $provider['class_name'] ?? '—' ); ?></code></td>
								</tr>
								<?php if ( ! empty( $provider['url'] ) ) : ?>
								<tr>
									<th scope="row"><?php esc_html_e( 'Website', 'wpseed' ); ?></th>
									<td><a href="<?php echo esc_url( $provider['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $provider['url'] ); ?></a></td>
								</tr>
								<?php endif; ?>
							</table>

							<?php
							// Show capabilities if the connector class exists.
							$caps = WPSeed_API_Directory::get_provider_capabilities( $provider_id );
							if ( ! empty( $caps ) ) :
							?>
								<h4 style="margin-top: 12px;"><?php esc_html_e( 'Capabilities', 'wpseed' ); ?></h4>
								<table class="wpseed-table wpseed-table-sm">
									<thead>
										<tr>
											<th scope="col"><?php esc_html_e( 'Action', 'wpseed' ); ?></th>
											<th scope="col"><?php esc_html_e( 'Method', 'wpseed' ); ?></th>
											<th scope="col"><?php esc_html_e( 'Description', 'wpseed' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $caps as $action => $cap ) : ?>
										<tr>
											<td><code><?php echo esc_html( $action ); ?></code></td>
											<td><?php echo esc_html( $cap['method'] ?? 'POST' ); ?></td>
											<td><?php echo esc_html( $cap['description'] ?? '' ); ?></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php endif; ?>

							<?php if ( $is_configured ) : ?>
								<div style="margin-top: 12px;">
									<button type="button"
										class="button wpseed-test-connector"
										data-provider="<?php echo esc_attr( $provider_id ); ?>">
										<span class="dashicons dashicons-yes-alt" style="margin-top: 3px;"></span>
										<?php esc_html_e( 'Test Connection', 'wpseed' ); ?>
									</button>
									<span class="wpseed-test-result" data-provider="<?php echo esc_attr( $provider_id ); ?>"></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

		<?php endif; ?>
	</div>

	<div class="wpseed-arch-section" style="margin-top: 20px;">
		<h3><?php esc_html_e( 'REST Bridge Endpoints', 'wpseed' ); ?></h3>
		<p><?php esc_html_e( 'Endpoints registered via the REST Bridge. Connector routes are auto-generated.', 'wpseed' ); ?></p>

		<?php
		$endpoints = wpseed_rest_endpoints();
		if ( empty( $endpoints ) ) :
		?>
			<p><em><?php esc_html_e( 'No endpoints registered yet. Endpoints are registered on rest_api_init.', 'wpseed' ); ?></em></p>
		<?php else : ?>
			<table class="wpseed-table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Method', 'wpseed' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Route', 'wpseed' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Capability', 'wpseed' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Source', 'wpseed' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Label', 'wpseed' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $endpoints as $key => $ep ) : ?>
					<tr>
						<td><code><?php echo esc_html( $ep['method'] ); ?></code></td>
						<td><code>/wp-json/<?php echo esc_html( $ep['namespace'] . $ep['route'] ); ?></code></td>
						<td><?php echo esc_html( $ep['capability'] ); ?></td>
						<td>
							<span class="wpseed-badge <?php echo 'connector' === $ep['source'] ? 'wpseed-status-info' : ''; ?>">
								<?php echo esc_html( $ep['source'] ); ?>
							</span>
						</td>
						<td><?php echo esc_html( $ep['label'] ); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

</div>
