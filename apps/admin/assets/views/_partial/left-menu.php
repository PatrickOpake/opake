<?php
if (!isset($org)) {
	$org = null;
}
?>
<div class="left-menu">
	<!--<div class='logo'>
		<img class="ng-cloak" opk-src='<?= isset($leftMenuPhoto) ? $leftMenuPhoto : $org->getLogo('default') ?>'
			 data-size='default' opk-src-error="/common/i/opake_logo_default.png" />
	</div>-->
	<ul>
		<?php
		$active = $this->getActiveMenu();
		foreach ($this->getMenuConfig() as $key => $item) {
			if (isset($item['title'])) {
				if ($_menu_check_access($item, $this->loggedUser, $org)) {
					$firstItemLink = sprintf($_menu_get_first_has_access($item, $this->loggedUser, $org), $org->id);
					if ($firstItemLink !== '#') {
						?>
						<li class="left-menu--item <?= ($active === $key ? ' active' : '') ?>">
						<span class="icon-container">
							<span class="icon <?= $key ?>"></span>
						</span>
							<a href="<?= $firstItemLink ?>"><?= $item['title'] ?></a>
							<span class="icon chevron"></span>
						</li>
						<?php
					}
				}
			}
		}
		?>
	</ul>
	<?php if (!empty($this->showSideCalendar)): ?>
		<side-calendar></side-calendar>
	<?php endif; ?>
	<?php if (!empty($this->showCaseListSideCalendar)): ?>
		<case-list-side-calendar></case-list-side-calendar>
	<?php endif; ?>
	<?php if (!empty($this->showScheduleLegend)): ?>
		<schedule-legend></schedule-legend>
	<?php endif; ?>
</div>