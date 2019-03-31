<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CI-15-2</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <script src="{{ asset('js/app.js?') }}"></script>
    <link href="/css/app.css" rel="stylesheet">
    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div id="main" class="flex-center position-ref">
    <div class="content" v-show="Array.isArray(tasks)">
        <div class="title m-b-md">
            Input master name
        </div>

        <form>
            <div class="form-group">
                <input v-model="name" type="text" class="form-control" id="exampleInputEmail1"
                       aria-describedby="emailHelp" placeholder="Enter master name">
            </div>
            <div @click="send" class="btn btn-primary">Submit</div>
        </form>
    </div>
    <div class="container" v-show="!Array.isArray(tasks)">
        <div class="accordion" style="margin-top: 60px" id="accordionExample">
            <div class="card" v-for="(item, key) in tasks">
                <div class="card-header" :id="'headingOne'+item.ID">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button"  data-toggle="collapse" :data-target="'#'+item.ID"
                                aria-expanded="true" :aria-controls="item.ID" @click="toggle(item.ID)">
                            @{{ key+': '+item.slave }}
                        </button>
                    </h2>
                </div>

                <div :id="item.ID" class="collapse show" :aria-labelledby="'headingOne'+item.ID"
                     data-parent="#accordionExample">
                    <div class="card-body">
                        <div class="list-group">
                            <a :id="'link_'+i.ID" :download="convert(i.RELATIVEPATH)"  v-for="i in item.files" :href="download(i.ID)" class="list-group-item list-group-item-action">@{{
                                i.RELATIVEPATH }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn btn-danger" style="margin-top: 10px; float: right" @click="back">AHTUNG</div>
    </div>
</div>
</body>
<script>
    var vMain = new Vue({
        el: '#main',
        data: {
            name: '',
            tasks: [],
            id: 1
        },
        methods: {
            send: function () {
                var _this = this;
                this.$http.get('/api/slaves/' + this.name)
                    .then(function (response) {
                        _this.tasks = response.data.data;
                    });
            },
            back: function () {
                this.tasks = [];
            },
            toggle: function (id) {
                $('#'+id).collapse('toggle');
            },
            convert: function (str) {
                var arr=str.substring(0, str.length - 1).split("\\");
                return arr[arr.length - 1];
            },
            download : function (id) {
                var _this = this;
                this.$http.get('/api/file/' + id)
                    .then(function (response) {
                       // $('#link_'+id)

                        var file = new File([response.data.document.CONTENT], this.convert(response.data.document.RELATIVEPATH));
                        var url = window.URL.createObjectURL(file);
                        document.getElementById('link_'+id).href=url;

                        //return file;

                        // var theBlob = new Blob();
                        // theBlob = response.data.document.CONTENT;
                        // theBlob.lastModifiedDate = new Date();
                        // var arr = response.data.document.RELATIVEPATH.split("\\");
                        // theBlob.name = arr[arr.length-1];
                        // console.log(theBlob);
                        // return theBlob;
                    });
            }
        }
    });
</script>
</html>
