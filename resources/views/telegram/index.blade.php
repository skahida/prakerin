@extends('layouts._app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Telegram</h4>
                    </div>
                    <div class="card-body">
                        <!-- Pesan sukses -->
                        @if (session('success'))
                        <div class="alert alert-primary">
                            <button type="button" aria-hidden="true" class="close" data-dismiss="alert">
                                <i class="nc-icon nc-simple-remove"></i>
                            </button>
                            <span>
                                <b> Sukses - </b> {{ session('success') }}
                        </div>
                        @endif

                        <!-- Pesan error untuk seluruh form -->
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form id="telegramForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Bot Token</label>
                                        <input type="text" id="botToken" name="botToken" class="form-control"
                                            value="{{ $telegram ? $telegram->bot_token : old('botToken') }}"
                                            placeholder="Masukkan Bot Token" />
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" id="username" name="username" class="form-control"
                                            value="{{ $telegram ? $telegram->username : old('username') }}"
                                            placeholder="Masukkan Username" />
                                    </div>
                                    <div class="form-group">
                                        <label>Message</label>
                                        <textarea id="message" name="message" class="form-control" placeholder="Masukkan Pesan">{{ $telegram ? $telegram->message : old('message') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info btn-fill"><i class="nc-icon nc-zoom-split"></i>
                                Check</button>
                            <div class="clearfix"></div>
                        </form>

                        <!-- Pesan Error / Success -->
                        {{-- <div id="responseMessage"></div> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Webhook Telegram</h4>
                    </div>
                    <div class="card-body">
                        <!-- Pesan sukses -->
                        @if (session('success'))
                        <div class="alert alert-primary">
                            <button type="button" aria-hidden="true" class="close" data-dismiss="alert">
                                <i class="nc-icon nc-simple-remove"></i>
                            </button>
                            <span>
                                <b> Sukses - </b> {{ session('success') }}
                        </div>
                        @endif

                        <!-- Pesan error untuk seluruh form -->
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form id="telegramSetWebhookForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Bot Token</label>
                                        <input type="text" id="botToken" name="botToken" class="form-control"
                                            value="{{ $telegram ? $telegram->bot_token : old('botToken') }}"
                                            placeholder="Masukkan Bot Token" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Message</label>
                                        <textarea id="message" name="message" class="form-control" placeholder="Masukkan Pesan" readonly>{{ $telegram ? $telegram->message : old('message') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-secondary btn-fill"><i
                                    class="nc-icon nc-zoom-split"></i>
                                Set Webhook</button>
                            <div class="clearfix"></div>
                        </form>

                        <!-- Pesan Error / Success -->
                        {{-- <div id="responseMessage"></div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection