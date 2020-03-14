<div class="left-menu">
	<div class='logo'>
		<img src='<?= $vendor->getLogo('default') ?>' data-size='default'/>
	</div>
	<ul>
		<li class="left-menu--item icon profile<?= $this->leftMenuActive === 'profile' ? ' active' : '' ?>">
			<a href='/vendors/<?= $org->id ?>/view/<?= $vendor->id ?>'>Profile</a>
		</li>
		<li class="left-menu--item icon inventory<?= $this->leftMenuActive === 'products' ? ' active' : '' ?>">
			<a href='/vendors/<?= $org->id ?>/products/<?= $vendor->id ?>'>Products</a>
		</li>
	</ul>
</div>