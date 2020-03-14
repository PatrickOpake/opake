<div class="left-menu">
	<ul>
		<?php
		$active = $this->getActiveMenu();
		foreach ($this->getMenuConfig() as $key => $item) {
			if ($_menu_check_access($item, $this->loggedUser)) {
				$firstItemLink = $_menu_get_first_has_access($item, $this->loggedUser);
				if ($firstItemLink !== '#') {
					?>
					<li class="left-menu--item <?= ($active === $key ? ' active' : '') ?>">
						<a href="<?= $firstItemLink ?>"><?= $item['title'] ?></a>
					</li>
					<?php
				}
			}
		}
		?>
	</ul>
</div>