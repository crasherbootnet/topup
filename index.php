<html>
<head>
    <link rel="stylesheet" href="./assets/css/style.css" />
</head>
<body ng-app="myApp" ng-controller="myCtrl">
    <div class="container" style="margin-top: 2%">
        <div class="row">
            <div class="offset-md-3 col-md-7">
            <h2 style="text-align: center;margin: 35px;">Souscription TopUp</h2>
                <form ng-submit="submit()">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Numéros de téléphone</label>
                        <input type="number" class="form-control" aria-describedby="emailHelp" ng-model="numeros">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Réseau</label>
                        <select name="" id="" class="form-control" ng-change="loadForfait()" ng-model="reseau" >
                            <option value="">Sélectionner le reseau</option>
                            <!--<option value="">Mtn</option>
                            <option value="">Moov</option>
                            <option value="">Orange</option>-->
                            <option ng-repeat="reseau in reseaux" value="{{ reseau.id }}"> {{ reseau.libelle }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Forfait</label>
                        <select name="" id="" class="form-control" ng-model="forfait">
                            <option value="">Sélectionner votre forfait</option>
                            <!--<option value="">Mtn</option>
                            <option value="">Moov</option>
                            <option value="">Orange</option>-->
                            <option ng-repeat="forfait in forfaits" value="{{ forfait.id }}"> XOF:{{ forfait.prix_xof }} -- USD:{{ forfait.prix_usd }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary offset-md-5 col-md-3" ng-click="subscription()" ng-if="!loading">
                        Enregistrer
                    </button>
                    <button type="submit" class="btn btn-primary offset-md-5 col-md-3" ng-click="subscription()" ng-if="loading" style="cursor: no-drop;" disabled>
                        Enregistrer
                        <div class="spinner-grow" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!--<script src="./assets/js/jquery.js"></script>-->
    <script src="./assets/js/angularjs.js"></script>

    <script>
        /*$(document).ready(function(e){
            $.get("http://localhost:8888/topup/api.php?action=get-all-reseau", function(data, status){
                // alert("Data: " + data + "\nStatus: " + status);
                let request = JSON.parse(data);
                console.log(request[0].id);
            });
        });*/
    </script>

    <script>
    var app = angular.module('myApp', []);
    app.controller('myCtrl', function($scope, $http) {
        $scope.firstName = "John";
        $scope.lastName = "Doe";
        $scope.reseaux = null;
        $scope.reseau = null;
        $scope.forfaits = null;
        $scope.loading = false;
        $http.get("http://localhost:8888/topup/api.php?action=get-all-reseau")
            .then(function(response) {
                $scope.reseaux = response.data;
            });
        $scope.loadForfait = function(){
            // alert("load forfait");
            if($scope.reseau){
                // console.log("===="+$scope.reseau)
                $http.get("http://localhost:8888/topup/api.php?action=get-all-forfait-by-resau&reseau_id="+$scope.reseau)
                .then(function(response) {
                    $scope.forfaits = response.data;
                });
            }else{
                $scope.forfaits = null;
            }
        }
        $scope.submit = function(){
            let validation = true;
            if(!$scope.numeros){
                alert("Entrez un numéros !");
                validation = false;
            }else if(!$scope.reseau){
                alert("Sélectionner un réseau !");
                validation = false;
            }else if(!$scope.forfait){
                alert("Sélectionner un forfait !");
                validation = false;
            }
            if(validation){
                $scope.loading = true;
                let data = {
                    "reseau_id": $scope.reseau,
                    "forfait_id": $scope.forfait,
                    "numeros": $scope.numeros
                }
                $http.post("http://localhost:8888/topup/api.php?action=save-subscription", data)
                    .then(function(response) {
                        $scope.forfaits = response.data;
                        alert("Votre suscription a été pris en compte !");
                        location.reload();
                    }).catch(function(e){
                        // handle errors in processing or in error.
                        $scope.loading = false;
                    });
            }
            
        }
    });
    </script>
</body>
</html>