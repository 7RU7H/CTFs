<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Zipper</title>
  <meta name="viewport" content="width=device-width, initial-scale=1", shrink-to-fit=no"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/css/bootstrap.min.css'>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'><link rel="stylesheet" href="./style.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

</head>
<body>
<!-- partial:index.partial.html -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
  <a class="navbar-brand" href="#">
    <i class="fa fa-codepen" aria-hidden="true"></i>
    Zipper
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="/index.php?file=home">Home <span class="sr-only">(current)</span></a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>

<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
  <div class="container">
    <h1 class="display-3">Welcome to Zipper!</h1>
    <p class="lead">
      With this online ZIP converter you can compress your files and create a ZIP archive. Reduce file size and save bandwidth with ZIP compression. 
      Your uploaded files are encrypted and no one can access them.
    </p>
    <hr class="my-4">
    <div class="page-container row-12">
    		<h4 class="col-12 text-center mb-5">Create Zip File of Multiple Uploaded Files </h4>
    		<div class="row-8 form-container">
                        		    	<form action="" method="post" enctype="multipart/form-data">
				    <div class="input-group">
						<div class="input-group-prepend">
						    <input type="submit" class="btn btn-primary" value="Upload">
						</div>
						<div class="custom-file">
						    <input type="file" class="custom-file-input" name="img[]" multiple>
						    <label class="custom-file-label" >Choose File</label>
						</div>
					</div>
				</form>
				
    		</div>
		</div>
  </div>


</div>

<div class="container">
  <footer>
    <p>&copy; Zipper 2021</p>
  </footer>
</div> <!-- /.container -->
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.13.0/umd/popper.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/js/bootstrap.bundle.min.js'></script>
</body>
</html>
