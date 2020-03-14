<h4>Order Info:</h4>

<div class="row">
	<div class="col-sm-2"><b>Date:</b> <?= $_date($order->date) ?></div>
	<div class="col-sm-3"><b>Vendor:</b> <?= $order->vendor->name ?></div>
	<div class="col-sm-2"><b># of Items:</b> <?= $order->items->count_all() ?></div>
	<div class="col-sm-3"><b>Shipping type:</b> <?= $order->shipping_type ?></div>
	<div class="col-sm-2"><b>Shipping cost:</b> <?= $order->shipping_cost ?></div>
</div>

<br />

<div class="panel-heading">
	<div class="row">
		<b class="col-sm-2">Name</b>
		<b class="col-sm-8">Description</b>
		<b class="col-sm-2">Quantity</b>
	</div>
</div>

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
	<?php foreach ($order->items->find_all() as $item) { ?>
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $item->id ?>">
			<div class="row">
				<div class="col-sm-2"><a onclick="event.stopPropagation();" href='/inventory/<?= $order->organization_id ?>/view/<?= $item->inventory_id; ?>'><?= $item->inventory->name ?></a></div>
				<div class="col-sm-8 ellipsis-text"><?= $item->inventory->desc ?></div>
				<div class="col-sm-2"><?= $item->units_received ?></div>
			</div>
		</div>
		<div id="collapse<?= $item->id ?>" class="panel-collapse collapse" role="tabpanel">
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-4">
						<b>Location, Quantity</b>
						<?php
						foreach ($item->packs->find_all() as $pack) {
							echo '<div>', $pack->location->name, ', ', $pack->quantity, '</div>';
						}
						?>
					</div>
					<div class="col-sm-4">
						<div><b>Received:</b> <?= $item->received ?></div>
						<div><b>Missing:</b> <?= $item->missing ?></div>
						<div><b>Damaged:</b> <?= $item->damaged ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>