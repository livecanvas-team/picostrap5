<?php
/**
 * Template Name: Bootstrap Demo Template
 *

 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

?>

 
<section class="py-5">
	<div class="container-fluid px-xl-3">
		<div class="row">
			<div class="col-lg-6">

				<div class="row">
					<div class="col-lg-12">
						<div class="lc-block mt-5 mt-lg-0 mb-4 border-top">
							<div editable="rich">
								<h2 class="fw-bolder display-5">Buttons</h2>
							</div>
						</div>
					</div>

				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="lc-block mb-4">
							<div editable="rich">
								<h2>Buttons</h2>
							</div>
						</div>
						<div class="lc-block mb-4"><button type="button" class="btn mb-2 btn-primary">Primary</button>
							<button type="button" class="btn mb-2 btn-secondary">Secondary</button>
							<button type="button" class="btn mb-2 btn-success">Success</button>
							<button type="button" class="btn mb-2 btn-danger">Danger</button>
							<button type="button" class="btn mb-2 btn-warning">Warning</button>
							<button type="button" class="btn mb-2 btn-info">Info</button>
							<button type="button" class="btn mb-2 btn-light">Light</button>
							<button type="button" class="btn mb-2 btn-dark">Dark</button>
							<button type="button" class="btn mb-2 btn-link">Link</button>
						</div>
						<div class="lc-block mb-4">
							<div editable="rich">
								<h2>Button Outline</h2>
							</div>
						</div>
						<div class="lc-block"><button type="button" class="btn mb-2 btn-outline-primary">Primary</button>
							<button type="button" class="btn mb-2 btn-outline-secondary">Secondary</button>
							<button type="button" class="btn mb-2 btn-outline-success">Success</button>
							<button type="button" class="btn mb-2 btn-outline-danger">Danger</button>
							<button type="button" class="btn mb-2 btn-outline-warning">Warning</button>
							<button type="button" class="btn mb-2 btn-outline-info">Info</button>
							<button type="button" class="btn mb-2 btn-outline-light">Light</button>
							<button type="button" class="btn mb-2 btn-outline-dark">Dark</button>
						</div>
					</div>

					<div class="col-lg-6">
						<div class="lc-block mb-4">
							<div editable="rich">
								<h2>Buttons Sizes</h2>
							</div>
						</div>
						<div class="lc-block mb-4"><button type="button" class="btn mb-3 btn-primary btn-sm">Small</button>
							<button type="button" class="btn mb-3 btn-primary ">Normal button</button>
							<button type="button" class="btn mb-3 btn-lg btn-primary">Large button</button>
						</div>


						<div class="lc-block mb-4">
							<div editable="rich">
								<h2>Buttons Group</h2>
							</div>
						</div>
						<div class="lc-block mb-4">
							<div class="btn-group mb-3" role="group" aria-label="Button group with nested dropdown">
								<div class="btn-group" role="group">
									<button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
										Dropdown
									</button>
									<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
										<li><a class="dropdown-item" href="#">Dropdown link</a></li>
										<li><a class="dropdown-item" href="#">Dropdown link</a></li>
									</ul>
								</div>
							</div>
							<div class="btn-group mb-3" role="group" aria-label="Button group with nested dropdown">
								<div class="btn-group" role="group">
									<button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
										Dropdown
									</button>
									<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
										<li><a class="dropdown-item" href="#">Dropdown link</a></li>
										<li><a class="dropdown-item" href="#">Dropdown link</a></li>
									</ul>
								</div>
							</div>
							<div class="btn-group mb-3" role="group" aria-label="Button group with nested dropdown">
								<div class="btn-group" role="group">
									<button id="btnGroupDrop1" type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
										Dropdown
									</button>
									<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
										<li><a class="dropdown-item" href="#">Dropdown link</a></li>
										<li><a class="dropdown-item" href="#">Dropdown link</a></li>
									</ul>
								</div>
							</div>
							<div class="btn-group mb-3" role="group" aria-label="Button group with nested dropdown">
								<div class="btn-group" role="group">
									<button id="btnGroupDrop1" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
										Dropdown
									</button>
									<ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
										<li><a class="dropdown-item" href="#">Dropdown link</a></li>
										<li><a class="dropdown-item" href="#">Dropdown link</a></li>
									</ul>
								</div>
							</div>
						</div>


					</div>
				</div>



				<div class="row"> </div>
				<div class="row">
					<div class="col-lg-6">
						<div class="lc-block mt-5 mb-4 border-top">
							<div editable="rich">
								<h2 class="fw-bolder display-5">Progress Bar</h2>
							</div>
						</div>
						<div class="lc-block   border-top">
							<div class="progress mb-3">
								<div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<div class="progress mb-3">
								<div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<div class="progress mb-3">
								<div class="progress-bar bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<div class="progress mb-3">
								<div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
						<div class="lc-block  mb-4">
							<div editable="rich">
								<h2>Striped</h2>
							</div>
						</div>
						<div class="lc-block   border-top">
							<div class="progress mb-3">
								<div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<div class="progress mb-3">
								<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<div class="progress mb-3">
								<div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<div class="progress mb-3">
								<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<div class="progress mb-3">
								<div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
						<div class="lc-block mt-5 mb-4 border-top">
							<div editable="rich">
								<h2 class="fw-bolder display-5">Alerts</h2>
							</div>
						</div>
						<div class="lc-block">
							<div class="alert alert-primary" role="alert">
								A simple primary alert—check it out!
							</div>
							<div class="alert alert-secondary" role="alert">
								A simple secondary alert—check it out!
							</div>
							<div class="alert alert-success" role="alert">
								A simple success alert—check it out!
							</div>
							<div class="alert alert-danger" role="alert">
								A simple danger alert—check it out!
							</div>
							<div class="alert alert-warning" role="alert">
								A simple warning alert—check it out!
							</div>
							<div class="alert alert-info" role="alert">
								A simple info alert—check it out!
							</div>

							<div class="alert alert-light" role="alert">
								A simple light alert—check it out!
							</div>
							<div class="alert alert-dark" role="alert">
								A simple dark alert—check it out!
							</div>
						</div>

					</div>
					<div class="col-lg-6">
						<div class="lc-block mt-5 mb-4 border-top">
							<div editable="rich">
								<h2 class="fw-bolder display-5">Badge</h2>
							</div>
						</div>
						<div class="lc-block pb-4">

							<span class="badge bg-primary">Primary</span>
							<span class="badge bg-secondary">Secondary</span>
							<span class="badge bg-success">Success</span>
							<span class="badge bg-danger">Danger</span>
							<span class="badge bg-warning text-dark">Warning</span>
							<span class="badge bg-info text-dark">Info</span>
							<span class="badge bg-light text-dark">Light</span>
							<span class="badge bg-dark">Dark</span>
						</div>
						<div class="lc-block ">

							<span class="badge rounded-pill bg-primary">Primary</span>
							<span class="badge rounded-pill bg-secondary">Secondary</span>
							<span class="badge rounded-pill bg-success">Success</span>
							<span class="badge rounded-pill bg-danger">Danger</span>
							<span class="badge rounded-pill bg-warning text-dark">Warning</span>
							<span class="badge rounded-pill bg-info text-dark">Info</span>
							<span class="badge rounded-pill bg-light text-dark">Light</span>
							<span class="badge rounded-pill bg-dark">Dark</span>
						</div>


						<div class="lc-block mt-5 mb-4 border-top">
							<div editable="rich">
								<h2 class="fw-bolder display-5">Cards</h2>
							</div>
						</div>
						<div class="row">
							<div class="lc-block mb-4">
								<div class="card">
									<img loading="lazy" class="card-img-top" src="https://images.unsplash.com/photo-1465403843003-b277b46a1120?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NzN8fGJsdWV8ZW58MHwwfHx8MTY0Mzk5ODg5Mg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=768" alt="Photo by Vincent Giersch" srcset="https://images.unsplash.com/photo-1465403843003-b277b46a1120?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NzN8fGJsdWV8ZW58MHwwfHx8MTY0Mzk5ODg5Mg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=768 1080w, https://images.unsplash.com/photo-1465403843003-b277b46a1120??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NzN8fGJsdWV8ZW58MHwwfHx8MTY0Mzk5ODg5Mg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=150 150w, https://images.unsplash.com/photo-1465403843003-b277b46a1120??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NzN8fGJsdWV8ZW58MHwwfHx8MTY0Mzk5ODg5Mg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=300 300w, https://images.unsplash.com/photo-1465403843003-b277b46a1120??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NzN8fGJsdWV8ZW58MHwwfHx8MTY0Mzk5ODg5Mg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=768 768w, https://images.unsplash.com/photo-1465403843003-b277b46a1120??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NzN8fGJsdWV8ZW58MHwwfHx8MTY0Mzk5ODg5Mg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1024 1024w" sizes="(max-width: 1080px) 100vw, 1080px" width="1080" height="768">
									<div class="card-body">
										<div class="lc-block">
											<div editable="rich">
												<h3 class="card-title">Card title</h3>
												<!-- <h4 class="card-subtitle" editable="inline">Card subtitle</h4> -->
												<p class="card-text">This is a simple Card example.</p>

											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="lc-block mb-4">
								<div class="card">
									<div class="card-body">
										This is some text within a card body.
									</div>
								</div>
							</div>
							<div class="lc-block mb-4">
								<div class="card">
									<h5 class="card-header">Featured</h5>
									<div class="card-body">
										<h5 class="card-title">Special title treatment</h5>
										<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
										<a href="#" class="btn btn-primary">Go somewhere</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="row">
					<div class="col-12">
						<div class="lc-block mt-5 mt-lg-0 mb-4 border-top">
							<div editable="rich">
								<h2 class="fw-bolder display-5">Typography</h2>
							</div>
						</div>
					</div>

				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="row mb-3">
							<div class="col-lg-12">
								<div class="lc-block mb-4">
									<div editable="rich">
										<h2>Headings</h2>
									</div>
								</div>
							</div>

							<div class="col-lg-12">
								<div class="lc-block">
									<div editable="rich">
										<h1>h1. Heading 1</h1>
										<h2>h2. Heading 2</h2>
										<h3>h3. Heading 3</h3>
										<h4>h4. Heading 4</h4>
										<h5>h5. Heading 5</h5>
										<h6>h6. Heading 6</h6>
										<hr>


									</div>
								</div>
							</div>



						</div>

						<div class="row mb-3">
							<div class="col-lg-12">
								<div class="lc-block mb-4">
									<div editable="rich">
										<h2>Paragraph</h2>
									</div>
								</div>
							</div>

							<div class="col-lg-12">
								<div class="lc-block">
									<div editable="rich">
										<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc et metus id ligula <a href="https://cdn.dopewp.com/media/architecture/gal1.jpg  ">malesuada</a> placerat sit amet quis enim. Aliquam erat volutpat. In pellentesque scelerisque auctor. Ut porta lacus eget nisi fermentum lobortis. Vestibulum facilisis tempor
											ipsum, ut rhoncus magna ultricies laoreet. Proin vehicula erat eget libero accumsan iaculis.
										</p>
									</div>
								</div>
								<div class="lc-block ">
									<div editable="rich">


										<p><small>This line of text is meant to be treated as fine print.</small></p>
										<p>The following is <strong>rendered as bold text</strong>.</p>
										<p>The following is <em>rendered as italicized text</em>.</p>
										<p>An abbreviation of the word attribute is <abbr title="attribute">attr</abbr>.</p>
									</div>
								</div>
								<div class="lc-block border-bottom">
									<div editable="rich">
										<p class="lead">
											This is a lead paragraph. It stands out from regular paragraphs.
										</p>
									</div>
								</div>
							</div>



						</div>

						<div class="row mb-5">
							<div class="col-lg-12">
								<div class="lc-block mb-4">
									<div editable="rich">
										<h2>Quote</h2>
									</div>
								</div>
							</div>

							<div class="col-lg-12">
								<div class="lc-block">
									<div editable="rich">
										<blockquote>
											<p class="blockquote blockquote-primary">
												“I will be the leader of a company that ends up being worth billions of dollars, because I got the answers. I understand culture. ”
												<br>
												<br>
												<small>
													– Noaa
												</small>
											</p>
										</blockquote>
									</div>
								</div>
							</div>



						</div>
						<div class="row mb-3">
							<div class="col-lg-12">
								<div class="lc-block mb-4">
									<div editable="rich">
										<h2>Code</h2>
									</div>
								</div>
							</div>

							<div class="col-lg-12">
								<div class="lc-block">
									<div editable="rich">
										<blockquote class="blockquote"><code>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc et metus id ligula malesuada placerat sit amet quis enim. Aliquam erat volutpat.</code><br></blockquote>
									</div>
								</div>
							</div>



						</div>
					</div>
					<div class="col-lg-6">

						<div class="row mb-3">
							<div class="col-lg-12">
								<div class="lc-block mb-4">
									<div editable="rich">
										<h2>Display</h2>
									</div>
								</div>
							</div>

							<div class="col-lg-12">
								<div class="lc-block">
									<div editable="rich">
										<h1 class="display-1">Display 1</h1>
										<h1 class="display-2">Display 2</h1>
										<h1 class="display-3">Display 3</h1>
										<h1 class="display-4">Display 4</h1>
										<h1 class="display-5">Display 5</h1>
										<h1 class="display-6">Display 6</h1>
										<hr>


									</div>
								</div>
								<div class="lc-block">
									<div class="lc-block mb-4">
										<div editable="rich">
											<h2>Text</h2>
										</div>
									</div>
								</div>
								<div class="lc-block">
									<div editable="rich">










										<span>Muted Text</span>
										<p class="text-muted">
											I will be the leader of a company that ends up being worth billions of dollars, because I got the answers…
										</p>
									</div>

									<span>Primary Text</span>
									<p class="text-primary">
										I will be the leader of a company that ends up being worth billions of dollars, because I got the answers…</p>


									<span>Info Text</span>
									<p class="text-info">
										I will be the leader of a company that ends up being worth billions of dollars, because I got the answers… </p>


									<span>Success Text</span>
									<p class="text-success">
										I will be the leader of a company that ends up being worth billions of dollars, because I got the answers… </p>


									<span>Warning Text</span>
									<p class="text-warning">
										I will be the leader of a company that ends up being worth billions of dollars, because I got the answers…
									</p>


									<span>Danger Text</span>
									<p class="text-danger">
										I will be the leader of a company that ends up being worth billions of dollars, because I got the answers… </p>



								</div>
							</div>



						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="lc-block mt-5 mb-4 border-top">
							<div editable="rich">
								<h2 class="fw-bolder display-5">Shadows</h2>
							</div>
						</div>
						<div class="lc-block">
							<div class="shadow-none p-3 mb-4 bg-light rounded">No shadow</div>
							<div class="shadow-sm p-3 mb-4 bg-body rounded">Small shadow</div>
							<div class="shadow p-3 mb-4 bg-body rounded">Regular shadow</div>
							<div class="shadow-lg p-3 mb-4 bg-body rounded">Larger shadow</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="row">
							<div class="col-lg-12">
								<div class="lc-block mt-5 mb-4 border-top">
									<div editable="rich">
										<h2 class="fw-bolder display-5">Images</h2>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-3">
								<div class="lc-block">
									<div editable="rich">
										<p> img-thumbnail</p>
									</div>
								</div>
								<div class="lc-block">
									<img loading="lazy" class="img-fluid img-thumbnail" src="https://images.unsplash.com/photo-1542324216541-c84c8ba6db04?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTF8fHJlZHxlbnwwfDJ8fHwxNjQzNjUxNzEy&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=1080" srcset="https://images.unsplash.com/photo-1542324216541-c84c8ba6db04?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTF8fHJlZHxlbnwwfDJ8fHwxNjQzNjUxNzEy&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=1080 1080w, https://images.unsplash.com/photo-1542324216541-c84c8ba6db04??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTF8fHJlZHxlbnwwfDJ8fHwxNjQzNjUxNzEy&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=150 150w, https://images.unsplash.com/photo-1542324216541-c84c8ba6db04??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTF8fHJlZHxlbnwwfDJ8fHwxNjQzNjUxNzEy&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=300 300w, https://images.unsplash.com/photo-1542324216541-c84c8ba6db04??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTF8fHJlZHxlbnwwfDJ8fHwxNjQzNjUxNzEy&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=768 768w, https://images.unsplash.com/photo-1542324216541-c84c8ba6db04??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTF8fHJlZHxlbnwwfDJ8fHwxNjQzNjUxNzEy&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1024 1024w" sizes="(max-width: 1080px) 100vw, 1080px" width="1080" height="1080" alt="Photo by Nikita Tikhomirov">
								</div>
							</div>
							<div class="col-lg-3">
								<div class="lc-block">
									<div editable="rich">
										<p> rounded-circle</p>
									</div>
								</div>
								<div class="lc-block">
									<img loading="lazy" class="img-fluid rounded-circle" src="https://images.unsplash.com/photo-1592826763154-b4971ef94531?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NTl8fGdyZWVufGVufDB8Mnx8fDE2NDM2NTE2OTU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=1080" srcset="https://images.unsplash.com/photo-1592826763154-b4971ef94531?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NTl8fGdyZWVufGVufDB8Mnx8fDE2NDM2NTE2OTU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=1080 1080w, https://images.unsplash.com/photo-1592826763154-b4971ef94531??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NTl8fGdyZWVufGVufDB8Mnx8fDE2NDM2NTE2OTU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=150 150w, https://images.unsplash.com/photo-1592826763154-b4971ef94531??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NTl8fGdyZWVufGVufDB8Mnx8fDE2NDM2NTE2OTU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=300 300w, https://images.unsplash.com/photo-1592826763154-b4971ef94531??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NTl8fGdyZWVufGVufDB8Mnx8fDE2NDM2NTE2OTU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=768 768w, https://images.unsplash.com/photo-1592826763154-b4971ef94531??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8NTl8fGdyZWVufGVufDB8Mnx8fDE2NDM2NTE2OTU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1024 1024w" sizes="(max-width: 1080px) 100vw, 1080px" width="1080" height="1080" alt="Photo by おにぎり">
								</div>
							</div>
							<div class="col-lg-3">
								<div class="lc-block">
									<div editable="rich">
										<p>rounded</p>
									</div>
								</div>
								<div class="lc-block">
									<img loading="lazy" class="img-fluid rounded" src="https://images.unsplash.com/photo-1584743579083-b331c4adbb15?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MzJ8fGJsdWV8ZW58MHwyfHx8MTY0MzY1MTczMg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=1080" srcset="https://images.unsplash.com/photo-1584743579083-b331c4adbb15?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MzJ8fGJsdWV8ZW58MHwyfHx8MTY0MzY1MTczMg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=1080 1080w, https://images.unsplash.com/photo-1584743579083-b331c4adbb15??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MzJ8fGJsdWV8ZW58MHwyfHx8MTY0MzY1MTczMg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=150 150w, https://images.unsplash.com/photo-1584743579083-b331c4adbb15??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MzJ8fGJsdWV8ZW58MHwyfHx8MTY0MzY1MTczMg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=300 300w, https://images.unsplash.com/photo-1584743579083-b331c4adbb15??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MzJ8fGJsdWV8ZW58MHwyfHx8MTY0MzY1MTczMg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=768 768w, https://images.unsplash.com/photo-1584743579083-b331c4adbb15??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MzJ8fGJsdWV8ZW58MHwyfHx8MTY0MzY1MTczMg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1024 1024w" sizes="(max-width: 1080px) 100vw, 1080px" width="1080" height="1080" alt="Photo by Christine Sandu">
								</div>
							</div>

							<div class="col-lg-3">
								<div class="lc-block">
									<div editable="rich">
										<p> img</p>
									</div>
								</div>
								<div class="lc-block">
									<img loading="lazy" class="img-fluid" src="https://images.unsplash.com/photo-1513754934927-4606bafe9858?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8eWVsbG93fGVufDB8Mnx8fDE2NDM2NTE2Mzg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=1080" srcset="https://images.unsplash.com/photo-1513754934927-4606bafe9858?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8eWVsbG93fGVufDB8Mnx8fDE2NDM2NTE2Mzg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=1080 1080w, https://images.unsplash.com/photo-1513754934927-4606bafe9858??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8eWVsbG93fGVufDB8Mnx8fDE2NDM2NTE2Mzg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=150 150w, https://images.unsplash.com/photo-1513754934927-4606bafe9858??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8eWVsbG93fGVufDB8Mnx8fDE2NDM2NTE2Mzg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=300 300w, https://images.unsplash.com/photo-1513754934927-4606bafe9858??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8eWVsbG93fGVufDB8Mnx8fDE2NDM2NTE2Mzg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=768 768w, https://images.unsplash.com/photo-1513754934927-4606bafe9858??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8eWVsbG93fGVufDB8Mnx8fDE2NDM2NTE2Mzg&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1024 1024w" sizes="(max-width: 1080px) 100vw, 1080px" width="1080" height="1080" alt="Photo by Catrin Johnson">
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
		<div class="row">
			<div class="col-xl-6">
				<div class="lc-block mt-5 mb-4 border-top">
					<div editable="rich">
						<h2 class="fw-bolder display-5">Navbar</h2>
					</div>
				</div>
				<div class="lc-block">
					<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
						<div class="container-fluid">
							<a class="navbar-brand" href="#">Navbar</a>
							<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>
							<div class="collapse navbar-collapse" id="navbarColor01">
								<ul class="navbar-nav me-auto mb-2 mb-lg-0">
									<li class="nav-item">
										<a class="nav-link active" aria-current="page" href="#">Home</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">Features</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">Pricing</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">About</a>
									</li>
								</ul>
								<form class="d-flex">
									<input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
									<button class="btn btn-outline-light" type="submit">Search</button>
								</form>
							</div>
						</div>
					</nav>
					<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
						<div class="container-fluid">
							<a class="navbar-brand" href="#">Navbar</a>
							<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>
							<div class="collapse navbar-collapse" id="navbarColor02">
								<ul class="navbar-nav me-auto mb-2 mb-lg-0">
									<li class="nav-item">
										<a class="nav-link active" aria-current="page" href="#">Home</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">Features</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">Pricing</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">About</a>
									</li>
								</ul>
								<form class="d-flex">
									<input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
									<button class="btn btn-outline-light" type="submit">Search</button>
								</form>
							</div>
						</div>
					</nav>
					<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
						<div class="container-fluid"><a class="navbar-brand" href="#">Navbar Light</a>
							<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor03" aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>
							<div class="collapse navbar-collapse" id="navbarColor03">
								<ul class="navbar-nav me-auto mb-2 mb-lg-0">
									<li class="nav-item">
										<a class="nav-link active" aria-current="page" href="#">Home</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">Features</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">Pricing</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">About</a>
									</li>
								</ul>
								<form class="d-flex">
									<input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
									<button class="btn btn-outline-primary" type="submit">Search</button>
								</form>
							</div>
						</div>
					</nav>
				</div>
				<div class="lc-block mt-5 mb-4 border-top">
					<div editable="rich">
						<h2 class="fw-bolder display-5">Accordion</h2>
					</div>
				</div>
				<div class="lc-block">
					<div class="accordion" id="accordion-cwb">
						<div class="accordion-item">
							<h2 class="accordion-header" id="headingOne">
								<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-cwb-1" aria-expanded="true" aria-controls="collapse-cwb-1">
									Accordion Item #1
								</button>
							</h2>
							<div id="collapse-cwb-1" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordion-cwb">
								<div class="accordion-body lc-block">
									<div editable="rich">
										<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc et metus id ligula malesuada placerat sit amet quis enim. Aliquam erat volutpat. In pellentesque scelerisque auctor. Ut porta lacus eget nisi fermentum lobortis. Vestibulum facilisis tempor
											ipsum, ut rhoncus magna ultricies laoreet. Proin vehicula erat eget libero accumsan iaculis.
										</p>
									</div>
								</div>
							</div>
						</div>
						<div class="accordion-item">
							<h2 class="accordion-header" id="headingTwo">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-cwb-2" aria-expanded="false" aria-controls="collapse-cwb-2">
									Accordion Item #2
								</button>
							</h2>
							<div id="collapse-cwb-2" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordion-cwb">
								<div class="accordion-body lc-block">
									<div editable="rich">
										<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc et metus id ligula malesuada placerat sit amet quis enim. Aliquam erat volutpat. In pellentesque scelerisque auctor. Ut porta lacus eget nisi fermentum lobortis. Vestibulum facilisis tempor
											ipsum, ut rhoncus magna ultricies laoreet. Proin vehicula erat eget libero accumsan iaculis.
										</p>
									</div>
								</div>
							</div>
						</div>
						<div class="accordion-item">
							<h2 class="accordion-header" id="headingThree">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-cwb-3" aria-expanded="false" aria-controls="collapse-cwb-3">
									Accordion Item #3
								</button>
							</h2>
							<div id="collapse-cwb-3" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordion-cwb">
								<div class="accordion-body lc-block">
									<div editable="rich">
										<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc et metus id ligula malesuada placerat sit amet quis enim. Aliquam erat volutpat. In pellentesque scelerisque auctor. Ut porta lacus eget nisi fermentum lobortis. Vestibulum facilisis tempor
											ipsum, ut rhoncus magna ultricies laoreet. Proin vehicula erat eget libero accumsan iaculis.
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>


				</div>
			</div>
			<div class="col-xl-6">
				<div class="lc-block mt-5 mb-4 border-top">
					<div editable="rich">
						<h2 class="fw-bolder display-5">Carousel</h2>
					</div>
				</div>
				<div class="lc-block mb-4">
					<div id="carousel-uwy" class="carousel slide" data-bs-ride="carousel">
						<div class="carousel-indicators">
							<button type="button" data-bs-target="#carousel-uwy" data-bs-slide-to="0" class="" aria-label="Slide 1"></button>
							<button type="button" data-bs-target="#carousel-uwy" data-bs-slide-to="1" aria-label="Slide 2" class=""></button>
							<button type="button" data-bs-target="#carousel-uwy" data-bs-slide-to="2" aria-label="Slide 3" class="active" aria-current="true"></button>
						</div>
						<div class="carousel-inner">
							<div class="carousel-item">
								<img loading="lazy" class="d-block w-100" src="https://images.unsplash.com/photo-1488330890490-c291ecf62571?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTR8fGZvcmVzdHxlbnwwfDB8fHwxNjQ0MDAwMTUw&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=768" alt="Photo by Andrew Coelho" srcset="https://images.unsplash.com/photo-1488330890490-c291ecf62571?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTR8fGZvcmVzdHxlbnwwfDB8fHwxNjQ0MDAwMTUw&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=768 1080w, https://images.unsplash.com/photo-1488330890490-c291ecf62571??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTR8fGZvcmVzdHxlbnwwfDB8fHwxNjQ0MDAwMTUw&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=150 150w, https://images.unsplash.com/photo-1488330890490-c291ecf62571??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTR8fGZvcmVzdHxlbnwwfDB8fHwxNjQ0MDAwMTUw&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=300 300w, https://images.unsplash.com/photo-1488330890490-c291ecf62571??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTR8fGZvcmVzdHxlbnwwfDB8fHwxNjQ0MDAwMTUw&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=768 768w, https://images.unsplash.com/photo-1488330890490-c291ecf62571??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MTR8fGZvcmVzdHxlbnwwfDB8fHwxNjQ0MDAwMTUw&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1024 1024w" sizes="(max-width: 1080px) 100vw, 1080px" width="1080" height="768">
								<div class="carousel-caption d-none d-md-block">
									<h5 editable="inline">First slide label</h5>
									<p editable="inline">Some representative placeholder content for the first slide.</p>
								</div>
							</div>
							<div class="carousel-item">
								<img loading="lazy" class="d-block w-100" src="https://images.unsplash.com/photo-1516214104703-d870798883c5?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8OHx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=768" alt="Photo by Imat Bagja Gumilar" srcset="https://images.unsplash.com/photo-1516214104703-d870798883c5?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8OHx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=768 1080w, https://images.unsplash.com/photo-1516214104703-d870798883c5??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8OHx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=150 150w, https://images.unsplash.com/photo-1516214104703-d870798883c5??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8OHx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=300 300w, https://images.unsplash.com/photo-1516214104703-d870798883c5??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8OHx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=768 768w, https://images.unsplash.com/photo-1516214104703-d870798883c5??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8OHx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1024 1024w" sizes="(max-width: 1080px) 100vw, 1080px" width="1080" height="768">
								<div class="carousel-caption d-none d-md-block">
									<h5 editable="inline">Second slide label</h5>
									<p editable="inline">Some representative placeholder content for the second slide.</p>
								</div>
							</div>
							<div class="carousel-item active">
								<img loading="lazy" class="d-block w-100" src="https://images.unsplash.com/photo-1448375240586-882707db888b?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=768" alt="Photo by Sebastian Unrau" srcset="https://images.unsplash.com/photo-1448375240586-882707db888b?crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080&amp;h=768 1080w, https://images.unsplash.com/photo-1448375240586-882707db888b??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=150 150w, https://images.unsplash.com/photo-1448375240586-882707db888b??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=300 300w, https://images.unsplash.com/photo-1448375240586-882707db888b??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=768 768w, https://images.unsplash.com/photo-1448375240586-882707db888b??crop=entropy&amp;cs=tinysrgb&amp;fit=crop&amp;fm=jpg&amp;ixid=MnwzNzg0fDB8MXxzZWFyY2h8MXx8Zm9yZXN0fGVufDB8MHx8fDE2NDM5OTc1MjU&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1024 1024w" sizes="(max-width: 1080px) 100vw, 1080px" width="1080" height="768">
								<div class="carousel-caption d-none d-md-block">
									<h5 editable="inline">Third slide label</h5>
									<p editable="inline">Some representative placeholder content for the third slide.</p>
								</div>
							</div>
						</div>
						<button class="carousel-control-prev" type="button" data-bs-target="#carousel-uwy" data-bs-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Previous</span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#carousel-uwy" data-bs-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Next</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section>
	<div class="container py-md-6 py-4">
		<div class="row justify-content-center">
			<div class="col-xl-10">
				<div class="lc-block mb-5">
					<div editable="rich">
						<h2 class="text-center rfs-20 fw-bolder">Lorem ipsum dolor sit amet, consectetur adipiscing elit suspendisse a lacus est. Etiam diam metus, lobortis </h2>
					</div>
				</div>
				<div class="row text-center text-md-start ">
					<div class=" col-md-6  ">
						<div class="lc-block">
							<div editable="rich">
								<p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse a lacus est. Etiam diam metus, lobortis non augue at, placerat viverra risus.&nbsp;</p>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="lc-block">
							<div editable="rich">
								<p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse a lacus est. Etiam diam metus, lobortis non augue at, placerat viverra risus.&nbsp;</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="text-light bg-dark">
	<div class="container py-md-6 py-4">
		<div class="row justify-content-center">
			<div class="col-xl-10">
				<div class="lc-block mb-5">
					<div editable="rich">
						<h2 class="text-center rfs-20 fw-bolder text-light">Lorem ipsum dolor sit amet, consectetur adipiscing elit suspendisse a lacus est. Etiam diam metus, lobortis </h2>
					</div>
				</div>
				<div class="row text-center text-md-start ">
					<div class=" col-md-6  ">
						<div class="lc-block">
							<div editable="rich">
								<p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse a lacus est. Etiam diam metus, lobortis non augue at, placerat viverra risus.&nbsp;</p>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="lc-block">
							<div editable="rich">
								<p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse a lacus est. Etiam diam metus, lobortis non augue at, placerat viverra risus.&nbsp;</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<?php

get_footer();
