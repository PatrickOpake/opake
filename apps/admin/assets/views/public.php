<!DOCTYPE html>
<html lang="en" ng-app="opake">
	<head>
		<title>Opake</title>
		<link rel="shortcut icon" href="/common/i/favicon.ico">
		<link rel="icon" type="image/png" href="/common/i/logo-blue-inverse.png" sizes="40x40">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta property="og:title" content="Increase revenue and reduce risk at a lower cost" />
		<meta property="og:description" content="Opake’s cloud-based platform leverages web and mobile functionalities to power ambulatory surgical center operations. The company is based in New York City and is led by its physician founder" />
		<meta property="og:image" content="https://opake.com/i/public/preview.jpg" />
		<?= $this->getCssHtml() ?>
		<?= $this->getJsHtml(true) ?>
	</head>
	<body>
		<div class="panel-auth">
			<div class="container">
				<div class="row">
					<div class="col-sm-12"><a class="auth-close title pull-right" href="">CLOSE X</a></div>
				</div>
				<div class="row">
					<div class="col-sm-2 title">SIGN IN</div>
					<div class="col-sm-6 error text-right"></div>
				</div>
				<form action="/">
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<input class="form-control" name="login" type="text" placeholder="USERNAME" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<input class="form-control" name="password" type="password" placeholder="PASSWORD" />
							</div>
							<div class="checkbox">
								<label class="pull-left"><input name='remember' type='checkbox' value='1' id='remember'/> remember me</label>
							</div>
							<a class='forgot pull-right' href='/user/restore'>forgot password</a>
						</div>
						<div class="col-sm-4 control">
							<button type="submit" class="btn btn-opk">Login</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<header class="navbar navbar-default">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">
						<img src="/common/i/logo.png" />
						<div class="logo-title">OPAKE</div>
					</a>
				</div>

				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right scroller">
						<li><a href="#">HOME</a></li>
						<li><a href="#marketing">MISSION</a></li>
						<li><a href="#product_peek">PRODUCTS</a></li>
						<li><a href="#calculator">SAVINGS</a></li>
						<li><a href="#contact">CONTACT</a></li>
						<li class="divider-vertical"></li>
						<li><a class="auth" href="">SIGN IN</a></li>
					</ul>
				</div>
			</div>
		</header>

		<!-- Content -->
		<div class="home bg-image">
			<div class="tagline">Clarity And Savings</div>
			<div class="more scroller"><a class="btn btn-opk" href="#contact">Learn more</a></div>
		</div>
		<div id="marketing" class="main-block">
			<div class="container">
				<div class="title">What we do</div>
				<div class="text">
					Opake powers ambulatory surgical center operations via its proprietary cloud-based platform, <br/> leveraging web and mobile products.
				</div>
				<div class="content">
					<div class="row">
						<div class="col-sm-3 marketing-list">
							<img src="/i/public/m1.jpg" />
							<div class="title">Scheduling</div>
							<ul>
								<li>Block time with an easy to use calendar</li>
								<li>Schedule cases with real-time updates across platforms, including the iPhone App</li>
							</ul>
						</div>
						<div class="col-sm-3 marketing-list">
							<img src="/i/public/m2.jpg" />
							<div class="title">Registration</div>
							<ul>
								<li>Reduce registration errors and time with data that prepopulate all system modules</li>
								<li>Support remote registration by physician offices</li>
							</ul>
						</div>
						<div class="col-sm-3 marketing-list">
							<img src="/i/public/m3.jpg" />
							<div class="title">Inventory</div>
							<ul>
								<li>Improve inventory cash flow with alerts</li>
								<li>Find item location and quantity details at a glance with the iPad app</li>
							</ul>
						</div>
						<div class="col-sm-3 marketing-list">
							<img src="/i/public/m4.jpg" />
							<div class="title">Documentation</div>
							<ul>
								<li>Expedite billing with integrated clinical documentation</li>
								<li>Enable providers to document remotely and share in real time</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="product_peek" class="main-block">
			<div class="container">
				<div class="title">Product Peek</div>
				<div class="text">
					Our integrated platform supports ASCs to increase revenue and reduce risk at a lower cost.
				</div>
				<div class="content">
					<div class="swiper-container">
						<div class="swiper-wrapper">
							<div class="swiper-slide">
								<div class="mask"><img src="/i/public/peek1.png" /></div>
								<div>Find and track inventory instantly</div>
							</div>
							<div class="swiper-slide">
								<div class="mask"><img src="/i/public/peek2.png" /></div>
								<div>Help reduce costs by providing staff transparency about how their choices impact spending</div>
							</div>
							<div class="swiper-slide">
								<div class="mask"><img src="/i/public/peek3.png" /></div>
								<div>Schedule cases with ease</div>
							</div>
							<div class="swiper-slide">
								<div class="mask narrow"><img src="/i/public/peek4.png" /></div>
								<div>View cases remotely</div>
							</div>
							<div class="swiper-slide">
								<div class="mask narrow"><img src="/i/public/peek5.png" /></div>
								<div>Document on the go</div>
							</div>
						</div>
						<div class="swiper-pagination"></div>
					</div>
				</div>
			</div>
		</div>
		<div id="calculator" class="main-block bg-image">
			<div class="container">
				<div class="title">Savings Calculator</div>
				<div class="content">
					<div class="calculator">
						<div class="title">Calculator</div>
						<div class="body">
							<div class="specialization">
								<div>
									<div><label><input type="checkbox" name="1" /> Cosmetic Surgery</label></div>
									<div><label><input type="checkbox" name="2" /> Dental/Oral Surgery</label></div>
									<div><label><input type="checkbox" name="3" /> Dermatology</label></div>
									<div><label><input type="checkbox" name="4" /> Endoscopy Center</label></div>
									<div><label><input type="checkbox" name="5" /> ENT</label></div>
									<div><label><input type="checkbox" name="6" /> Gastroenterology</label></div>
									<div><label><input type="checkbox" name="7" /> General Surgery</label></div>
									<div><label><input type="checkbox" name="8" /> Gynecology</label></div>
									<div><label><input type="checkbox" name="9" /> Multi-Specialty</label></div>
									<div><label><input type="checkbox" name="10" /> Neurology</label></div>
								</div>
								<div>
									<div><label><input type="checkbox" name="11" /> Laser Eye Surgery</label></div>
									<div><label><input type="checkbox" name="12" /> Ophthalmology</label></div>
									<div><label><input type="checkbox" name="13" /> Oral and maxillofacial surgery</label></div>
									<div><label><input type="checkbox" name="14" /> Orthopedic</label></div>
									<div><label><input type="checkbox" name="15" /> Pain Management</label></div>
									<div><label><input type="checkbox" name="16" /> Plastic Surgery</label></div>
									<div><label><input type="checkbox" name="17" /> Podiatry</label></div>
									<div><label><input type="checkbox" name="18" /> Urology</label></div>
									<div><label><input type="checkbox" name="19" /> Other</label></div>
								</div>
							</div>
							<div class="questions">
								<div class="set-specialization">
									<div>What is your centers specialization?</div>
									<div><div class="pull-right">></div></div>
								</div>
								<div>
									<div>What is your monthly spend on pharmaceuticals?</div>
									<div>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" class="form-control" name="pharm" maxlength="10" />
										</div>
									</div>
								</div>
								<div>
									<div>What is your monthly spend on medical devices?</div>
									<div>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" class="form-control" name="devices" maxlength="10" />
										</div>
									</div>
								</div>
								<div>
									<div>What is your net operating margin (revenue/margin)?</div>
									<div>
										<div class="input-group">
											<input type="text" class="form-control" name="margin" maxlength="2" />
											<span class="input-group-addon">%</span>
										</div>
									</div>
								</div>
							</div>
							<div class="results">
								<div class="text">Based on your inputs, the Opake system could save you...</div>
								<div class="item">Up to <span class="pharm"></span> /month on pharmaceuticals*</div>
								<div class="item">Up to <span class="devices"></span> /month on medical devices*</div>
								<div class="item">Up to <span class="margin"></span> /month in previously lost revenue**</div>
							</div>
						</div>
						<div class="control">
							<div class="specialization">
								<div class="back">Back</div>
								<div class="save">Save</div>
							</div>
							<div class="questions">
								<div class="reset">Reset</div>
								<div class="calc">Calculate</div>
							</div>
							<div class="results">
								<div class="restart">Start over</div>
								<div class="more">Learn more</div>
							</div>
						</div>
						<div class="notes">
							<div>*Based on studies showing that potentially 20% of inventory expires</div>
							<div>**Based on studies showing that for every dollar saved in supply chain cost is recouped by operating revenue %</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="contact" class="main-block">
			<div class="container">
				<div class="title">Contact</div>
				<div class="content">
					<div class="row">
						<div class="col-sm-6">
							<img class="map" src="/i/public/map.jpg" alt="Map" />
						</div>
						<div class="col-sm-6">
							<form class="contact-form">
								<div class="row">
									<div class="col-sm-12">
										Interested in learning about what we do? We'd love to hear from you! Drop us a line.
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label>Name</label>
											<input class="form-control" name="message[name]" type="text">
											<div class="help"></div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label>Email</label>
											<input class="form-control" name="message[email]" type="text">
											<div class="help"></div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label>Message</label>
											<textarea class="form-control" name="message[text]"></textarea>
											<div class="help"></div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-8 result"></div>
									<div class="col-sm-4 control">
										<button type="submit" class="btn btn-opk">Send</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Content end -->

		<footer class="footer">
			<div class="container">
				<div class="links">
					<ul class="menu scroller">
						<li><a href="#">HOME</a></li>
						<li><a href="#marketing">MISSION</a></li>
						<li><a href="#product_peek">PRODUCTS</a></li>
						<li><a href="#calculator">SAVINGS</a></li>
						<li><a href="#contact">CONTACT</a></li>
					</ul>
					<ul class="socials">
						<li><a href="https://itunes.apple.com/us/app/opake-md/id1069352739?mt=8" target="_blank"><img src="/i/itunes-button.png" alt=""></a></li>
						<li><a class="icn-social-mail" href="mailto:<?= $contactEmail ?>"></a></li>
					</ul>
				</div>
				<div class="bottom">
					<div class="copyright-ama">
						<div>CPT copyright <?= date('Y') ?> American Medical Association.</div>
						<div>All rights reserved. Fee schedules, relative value units, conversion factors and/or related components are not assigned by the AMA, are not part of CPT, and the AMA is not recommending their use. The AMA does not directly or indirectly practice medicine or dispense medical services. The AMA assumes no liability for data contained or not contained herein.</div>
						<div>CPT is a registered trademark of the American Medical Association.</div>
					</div>
					<div class="copyright">&copy; Opake <?= date('Y') ?></div>
				</div>
			</div>
		</footer>

		<?= $this->getJSHtml(false) ?>
	</body>
</html>
