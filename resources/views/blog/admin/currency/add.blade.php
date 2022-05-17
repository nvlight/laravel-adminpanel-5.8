@extends('layouts.app_admin')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        @component('blog.admin.components.breadcrumbs')
            @slot('title') Добавление новой валюты @endslot
            @slot('parent') Главная @endslot
            @slot('currency') Список валют @endslot
            @slot('active') Добавление новой валюты @endslot
        @endcomponent
    </section>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div>
                        @php
                            //dump(\Session::all());
                        @endphp
                    </div>
{{--                    {{url('/admin/currency/add')}}--}}
                    <form action="{{route('blog.admin.currency-add')}}" method="post" data-toggle="validator">
                        @csrf
                        <div class="box-body">
                            <div class="form-group has-feedback">
                                <label for="title">Наименование валюты</label>
                                <input type="text" name="title" class="form-control" id="title" placeholder="Наименование валюты" value="{{old('title')}}" required>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>

                            <div class="form-group">
                                <label for="code">Код валюты</label>
                                <input type="text" name="code" class="form-control" id="code" placeholder="Код валюты" value="{{old('code')}}" required>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>

                            <div class="form-group">
                                <label for="symbol_left">Символ слева</label>
                                <input type="text" name="symbol_left" class="form-control" id="symbol_left" placeholder="Символ слева" value="{{old('symbol_left')}}">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>

                            <div class="form-group has-feedback">
                                <label for="symbol_right">Символ справа</label>
                                <input type="text" name="symbol_right" class="form-control" id="symbol_right" placeholder="Символ справа" value="{{old('symbol_right')}}">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>

                            <div class="form-group has-feedback">
                                <label for="value">Значение</label>
                                <input type="text" name="value" class="form-control" id="value" placeholder="Значение" title="если это базовая валюта поставьте 1, то курс к базовой валюте."
                                       value="{{old('value')}}"
                                       required data-error="Допускаются цифры и десятичная точка" pattern="^[0-9.]{1,}">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>

                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group has-feedback">
                                <label for="base">
{{--                                    @if (old('base') == 'on') value="1" @else value="0" @endif--}}
                                    <input type="checkbox" name="base" id="base"
                                    >
                                    Базовая валюта</label>
                            </div>

                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-success">Добавить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->




@endsection
