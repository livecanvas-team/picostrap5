
<!-- static example  https://dev.to/typo3freelancer/updated-bootstrap-5-1-1-navbar-multi-level-and-mega-menu-4j1o -->
 

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Bootstrap <span class="badge bg-primary">Static Mega Menu Demo</span></a>
    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-content">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar-content">
      <ul class="navbar-nav mr-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">Multilevel</a>
          <ul class="dropdown-menu shadow">
            <li><a class="dropdown-item" href="#">Gallery</a></li>
            <li><a class="dropdown-item" href="blog.html">Blog</a></li>
            <li class="dropstart">
              <a href="#" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">Submenu Left</a>
              <ul class="dropdown-menu shadow">
                <li><a class="dropdown-item" href=""> Third level 1</a></li>
                <li><a class="dropdown-item" href=""> Third level 2</a></li>
                <li><a class="dropdown-item" href=""> Third level 3</a></li>
                <li><a class="dropdown-item" href=""> Third level 4</a></li>
                <li><a class="dropdown-item" href=""> Third level 5</a></li>
              </ul>
            </li>
            <li class="dropend">
              <a href="#" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">Submenu Right</a>
              <ul class="dropdown-menu shadow">
                <li><a class="dropdown-item" href=""> Second level 1</a></li>
                <li><a class="dropdown-item" href=""> Second level 2</a></li>
                <li><a class="dropdown-item" href=""> Second level 3</a></li>
                <li class="dropend">
                  <a href="#" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">Let's go deeper!</a>
                  <ul class="dropdown-menu dropdown-submenu shadow">
                    <li><a class="dropdown-item" href=""> Third level 1</a></li>
                    <li><a class="dropdown-item" href=""> Third level 2</a></li>
                    <li><a class="dropdown-item" href=""> Third level 3</a></li>
                    <li><a class="dropdown-item" href=""> Third level 4</a></li>
                    <li class="dropend">
                      <a href="#" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">Still don't have enough? Go much deeper!</a>
                      <ul class="dropdown-menu dropdown-submenu shadow">
                        <li><a class="dropdown-item" href=""> Third level 1</a></li>
                        <li><a class="dropdown-item" href=""> Third level 2</a></li>
                        <li><a class="dropdown-item" href=""> Third level 3</a></li>
                        <li><a class="dropdown-item" href=""> Third level 4</a></li>
                        <li><a class="dropdown-item" href=""> Third level 5</a></li>
                      </ul>
                    </li>
                  </ul>
                </li>
                <li><a class="dropdown-item" href=""> Third level 5</a></li>
              </ul>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item dropdown dropdown-mega position-static">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">Megamenu</a>
          <div class="dropdown-menu shadow">
            <div class="mega-content px-4">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-12 col-sm-4 col-md-3 py-4">
                    <h5>No additional JS</h5>
                    <div class="list-group">
                      <a class="list-group-item" href="#">Accomodations</a>
                      <a class="list-group-item" href="#">Terms & Conditions</a>
                      <a class="list-group-item" href="#">Privacy</a>
                    </div>
                  </div>
                  <div class="col-12 col-sm-4 col-md-3 py-4">
                    <h5>No Additional CSS</h5>
                    <div class="card">
                <img src="https://images.unsplash.com/photo-1509599589979-3b5a15d2816e?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&ixid=MnwzNzg0fDB8MXxzZWFyY2h8NDR8fGZvcmVzdHxlbnwwfDB8fHwxNjM0OTkxMjA3&ixlib=rb-1.2.1&q=80&w=1080&h=768" class="img-fluid" alt="image">
                <div class="card-body">
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
              </div>
                  </div>
                  <div class="col-12 col-sm-4 col-md-3 py-4">
                    <h5>Lot of Pages</h5>
                    <p>Lorem ipsum dolo sit achmet muhamed borlan de irtka.
                  </div>
                  <div class="col-12 col-sm-12 col-md-3 py-4">
                    <h5>Damn, so many</h5>
                    <div class="list-group">
                      <a class="list-group-item" href="#">Accomodations</a>
                      <a class="list-group-item" href="#">Terms & Conditions</a>
                      <a class="list-group-item" href="#">Privacy</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      <form class="d-flex ms-auto">
          <div class="input-group">
              <input class="form-control border-0 mr-2" type="search" placeholder="Search" aria-label="Search">
              <button class="btn btn-primary border-0" type="submit">Search</button>
          </div>
      </form>
    </div>
  </div>
</nav>