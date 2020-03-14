<?php
if (!isset($org)) {
	$org = null;
}

if (!isset($topMenu)) {
	$topMenu = $this->getMenuConfig(1);
	$topActive = $this->getActiveMenu(1);

	$topMenu2 = $this->getMenuConfig(2);
	$topActive2 = $this->getActiveMenu(2);

	$topMenu3 = $this->getMenuConfig(3);
	$topActive3 = $this->getActiveMenu(3);

}
?>

<div class="top-menu-group" ng-if="!view.isPhone()">
	<div class="top-menu" menu-key="topMenu" active-menu-key="topMenuActive">
		<?php if (!empty($topMenu)):?>
			<ul>
				<?php
				foreach ($topMenu as $key => $item) {
					if ($_menu_check_access($item, $this->loggedUser, $org)) {
						$firstItemLink = sprintf($_menu_get_first_has_access($item, $this->loggedUser, $org), isset($org) ? $org->id : '');
						if ($firstItemLink !== '#') {
							?>
							<li <?= $topActive === $key ? 'class="active"' : '' ?>>
								<a href="<?= sprintf($firstItemLink, isset($org) ? $org->id : '') ?>">
									<span>
										<?= $item['title'] ?>
										<?php if (!empty($item['top_counter'])): ?>
											<span ng-init="menuCounter.getCount('<?= $key ?>')">
												<span class="badge" ng-if="menuCounter.<?= $key ?>Count > 0" ng-cloak>{{ menuCounter.<?= $key ?>Count }}</span>
											</span>
										<?php endif; ?>
									</span>
								</a>
							</li>
						<?php
						}
					}
				}
				?>
			</ul>
		<?php endif ?>
	</div>
	<div class="top-menu" menu-key="subTopMenu" active-menu-key="subTopMenuActive">
		<?php if (!empty($topMenu2)): ?>
			<ul>
				<?php
				foreach ($topMenu2 as $key => $item) {
					if ($_menu_check_access($item, $this->loggedUser, $org)) {
						$firstItemLink = sprintf($_menu_get_first_has_access($item, $this->loggedUser, $org), $org->id);
						if ($firstItemLink !== '#') {
							?>
							<li <?= $topActive2 === $key ? 'class="active"' : '' ?>>
								<a href="<?= sprintf($firstItemLink, isset($org) ? $org->id : '') ?>"><span><?= $item['title'] ?></span></a>
							</li>
							<?php
						}
					}
				}
				?>
			</ul>
		<?php endif ?>
	</div>
	<div class="top-menu">
		<?php if (!empty($topMenu3)): ?>
				<ul>
					<?php
						foreach ($topMenu3 as $key => $item) {
							if ($_menu_check_access($item, $this->loggedUser, $org)) {
								$firstItemLink = sprintf($_menu_get_first_has_access($item, $this->loggedUser, $org), $org->id);
								if ($firstItemLink !== '#') {
									?>
									<li <?= $topActive3 === $key ? 'class="active"' : '' ?>>
										<a href="<?= sprintf($firstItemLink, isset($org) ? $org->id : '') ?>"><span><?= $item['title'] ?></span></a>
									</li>
									<?php
								}
							}
						}
					?>
				</ul>
	    <?php endif ?>
	</div>
</div>
<div class="top-menu-group" ng-if="view.isPhone()"></div>