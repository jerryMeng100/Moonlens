<?php


/*function findDirectories($dir) { //Scans through current directory and finds which items are 
directories themselves

	$cdir = scandir($dir);

	$result = array();

	foreach ($cdir as $key) {
      	if (is_dir($key)) {
      		array_push($result, $key);
      	}
    }
    return $result;
}

function defineNewDirectory() { //Defines new directory that the pair of files can be saved into
	$n = 1;
	$directories = findDirectories($dir);
	for ($i = 0; $i < count($directories); $i++) {
		if ($dir != $n) {
			return strval($n);
		} else {
			$n++;
			$i--;
		}
	}
}*/

$location;

if (isset($_FILES['video'])) {
    $file_video = $_FILES['video'];
    $file_name_video = $file_video['name'];
    $file_tmp_video = $file_video['tmp_name'];

    $location = "uploads/";
    

    $file_destination_video = $location . $file_name_video;

    if (move_uploaded_file($file_tmp_video, $file_destination_video)) {
    } else {
    }
  }

if (isset($_FILES['accelerometer'])) {
    $file_accelerometer = $_FILES['accelerometer'];
    $file_name_acceleromter = $file_accelerometer['name'];
    $file_tmp_accelerometer = $file_accelerometer['tmp_name'];

   

    $file_destination_accelerometer = $location . $file_name_accelerometer;

    if (move_uploaded_file($file_tmp_accelerometer, $file_destination_accelerometer)) {
    }
  }

?>


<!DOCTYPE HTML>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <title>Moonlens: Moccasin Upload System</title>
    <style>
        @font-face {
            font-family: 'Lato';
        }
        *{
            font-family: Lato;
            font-size: 19px;
        }
        p{
            margin: 0;
        }
        body {
            margin: 0px;
            background-color: #fff;
            overflow: hidden;
        }
        div.middle {
            position: relative;
            top: 700px;
            text-align: center;
        }


        div#upload1{
            float: left;
            cursor: pointer;
        }
        div#upload2{
            float: right;
            cursor: pointer;
        }
        div#submitBox{
            cursor: pointer;
        }

    </style>
</head>
<body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="http://threejs.org/build/three.min.js"></script>
<script src="http://threejs.org/examples/js/renderers/CSS3DRenderer.js"></script>
<div id="logo">
    <img src="logo/logo-transparent.png" alt="Logo" style="width:300px;height:300px;">
</div>

<div id="buttons">
    <div class = "middle">

    <p style = "color: white;">Submission Successful. Thank you!</p>  
            
            
    </div>
</div>
<script>
    var camera;
    var scene;
    var renderer;

    var clock;
    var deltaTime;

    var particleSystem;

    var fallingStatus = [];

    var backgroundScene;
    var backgroundCamera;
    var backgroundMesh;
    var texture;

    //text overlay
    var scene2;
    var logoElem;
    var logoDiv;
    var buttonsElem;
    var buttonsDiv;
    var renderer2;

    init();
    animate();

    function init(){

        clock = new THREE.Clock(true);

        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(70, window.innerWidth/window.innerHeight, 1, 1000);
        camera.position.z = 300;

        var light = new THREE.DirectionalLight(0xffffff);
        light.position.set(1, -1, 1).normalize();
        scene.add(light);

        particleSystem = createParticleSystem();
        createFallingStatuses();
        scene.add(particleSystem);

        createBackground();

        renderer = new THREE.WebGLRenderer();
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        createDiv();

        window.addEventListener("resize", onWindowResize, false);

        render();
    }


    function createParticleSystem(){
        var particleCount = 500;
        var particles = new THREE.Geometry();
        for (var p = 0; p < particleCount; p++){

            var x = Math.random() * 600 - 300;
            var y = Math.random() * 400 - 200;
            var z = Math.random() * 400 - 200;

            var particle = new THREE.Vector3(x, y, z);

            particles.vertices.push(particle);
        }
        var particleMaterial = new THREE.PointsMaterial({
            color: 0xffffff,
            size: 2,
            map: THREE.ImageUtils.loadTexture("vectors/star.png"),
            blending: THREE.AdditiveBlending,
            transparent: true,
        });

        particleSystem = new THREE.Points(particles, particleMaterial);
        return particleSystem;

    }

    function animateParticles(){
        var verts = particleSystem.geometry.vertices;
        for (var i = 0; i < verts.length; i ++){
            var vert = verts[i];
            //move vert up and down based on value in its status
            //fallingStatus moves the particle up and down and also acts as a counter,
            //and values 0 to 59 mean going up, -1 to -60 mean going down.  It alternates when it reaches
            //the end of one counting phase i.e. 59 goes to -1 next time, and -60 goes to 0 next time.
            if (fallingStatus[i] < 0){
                vert.y -= 1.5*deltaTime;
                if (fallingStatus[i] <= -60) fallingStatus[i] = 0;
                else fallingStatus[i]--;
            }
            else{
                vert.y += 1.5*deltaTime;
                if (fallingStatus[i] >= 59) fallingStatus[i] = -1;
                else fallingStatus[i]++;
            }
        }
        particleSystem.geometry.verticesNeedUpdate = true;
    }

    function createFallingStatuses(){
        //create initial status for state of each star
        var verts = particleSystem.geometry.vertices;
        for (var i = 0; i < verts.length; i++){
            fallingStatus.push(Math.random()*60 - 30);
        }
    }

    function createBackground(){
        //background courtesy of ThisIsSparta on StackOverflow
        texture = THREE.ImageUtils.loadTexture('backgrounds/gradient.png');
        backgroundMesh = new THREE.Mesh(
            new THREE.PlaneGeometry(2, 2, 0),
            new THREE.MeshBasicMaterial({
                map: texture
            })
        );

        backgroundMesh.material.depthTest = false;
        backgroundMesh.material.depthWrite = false;

        // Create background scene
        backgroundScene = new THREE.Scene();
        backgroundCamera = new THREE.Camera();
        backgroundScene.add(backgroundCamera);
        backgroundScene.add(backgroundMesh);
    }

    function createDiv(){
        //div functionality courtesy of Jean Lescure
        scene2 = new THREE.Scene();

        logoElem = document.getElementById("logo");
        logoDiv = new THREE.CSS3DObject(logoElem);
        logoDiv.position.x = 0;
        logoDiv.position.y = 70;
        logoDiv.position.z = -185;

        buttonElem = document.getElementById("buttons");
        buttonDiv = new THREE.CSS3DObject(buttonElem);
        buttonDiv.position.x = 0;
        buttonDiv.position.y = 560;
        buttonDiv.position.z = -185;

        scene2.add(logoDiv);
        scene2.add(buttonDiv);

        renderer2 = new THREE.CSS3DRenderer();
        renderer2.setSize(window.innerWidth, window.innerHeight);
        renderer2.domElement.style.position = 'absolute';
        renderer2.domElement.style.top = 0;
        document.body.appendChild(renderer2.domElement);
    }

    function repositionDiv(){
        logoDiv.position.x = 0;
        logoDiv.position.y = 70;
        logoDiv.position.z = -185;
        buttonDiv.position.x = 0;
        buttonDiv.position.y = 525;
        buttonDiv.position.z = -185;
        renderer2.setSize(window.innerWidth, window.innerHeight);
        renderer2.domElement.style.position = 'absolute';
        renderer2.domElement.style.top = 0;
    }


    function render(){
        renderer.autoClear = false;
        renderer.clear();
        renderer.render(backgroundScene, backgroundCamera);
        renderer2.render(scene2, camera);
        renderer.render(scene, camera);
    }

    function animate(){
        deltaTime = clock.getDelta();

        animateParticles();

        render();
        requestAnimationFrame(animate);

    }

    function onWindowResize(){
        repositionDiv();
        camera.aspect = window.innerWidth/window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
        render();
    }

</script>
</body>
</html>


