<?php include('_partial/header.php'); ?>

	<div class="site-body main" sticky-scroll>
		<div alert-flash ng-animate message=""></div>
		<div class="container-fluid">
			<div class="inner-grid">
				<div>
					<?php include(__DIR__ . '/_partial/top-menu.php'); ?>
					<div class="content-wrap">
						<?= $this->getErrorsHtml() ?>
						<?= $this->getMessageHtml() ?>
						<?php include(__DIR__ . sprintf('/%s.php', $subview)) ?>
					</div>
				</div>
			</div>

		</div>
	</div>

<?php include('_partial/footer.php'); ?>