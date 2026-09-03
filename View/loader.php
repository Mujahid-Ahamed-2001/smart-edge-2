<style>
.loader {
    top: 0;
    left: 0;
    padding: 0;
    margin: 0;
    height: 100vh;
    width: 100%;
    display: grid;
    place-items: center;
    position: fixed;
    z-index: 10000000;
    /* background: url(../Assets/Images/SystemImages/Backgrounds/Wallpaper.jpg) center/cover no-repeat; */
}

/* Overlay */
.loader::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.4); /* Change transparency here */
    z-index: 1;
}

/* Make sure loader content is above overlay */
.loader > * {
    position: relative;
    z-index: 2;
}
.containers {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #506fd9;
  bottom: 16px solid #506fd9;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 1s linear infinite; /* Safari */
  animation: spin 1s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }  
  100% { transform: rotate(360deg); }
}
.loader .w-10
{
  width:25% !important;
}
.loader .text-center
{
    width: 50%;
    margin-top: -170px;
    text-align: center !important;
}
</style>
<div class="loader dark_background" id="loader">
  <!-- <img class="w-10" src="../Assets/Images/smart_edge_logo.png" alt="Smart Edge logo"> -->
  <div class="containers">
  </div>
</div>
<script>
  $(document).ready(function(){
    $('#loader').delay(800).fadeOut(800);
  });
</script>