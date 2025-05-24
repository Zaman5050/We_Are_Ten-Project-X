@extends('layouts.tenant.index')

@section('title', 'Taxes | Settings')

@section('content')

    <h2 class="signup-headings">Texas</h2>

    <form action="" method="POST">
        <div class="row">
            <div class="col-sm-6">
                <div class="sign-up-input-container">
                    <label for="vat" class="signup-labels">Vat</label>
                    <input id="vat" type="text" name="vat"
                        placeholder="Vat" class="signup-inputs" required />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4">
                <button class="log-in" type="submit">
                    Save
                </button>
            </div>
        </div>


    </form>

@endsection
