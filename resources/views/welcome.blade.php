@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome</div>

                <div class="panel-body">
                <h1>Introduction</h1>
               <p> As Cloud Computing becomes prevalent, more and more sensitive information are being centralized into the cloud. Although traditional searchable encryption schemes allow a user to securely search over encrypted data through keywords and selectively retrieve files of interest, these techniques support only exact keyword search. In this paper, for the first time we formalize and solve the problem of effective fuzzy keyword search over encrypted cloud data while maintaining keyword privacy. </p>
                   <p>   Fuzzy keyword search greatly enhances system usability by returning the matching files when users’ searching inputs exactly match the predefined keywords or the closest possible matching files based on keyword similarity semantics, when exact match fails. In our solution, we exploit edit distance to quantify keywords similarity and develop two advanced techniques on constructing fuzzy keyword sets, which achieve optimized storage and representation overheads. We further propose a brand new symbol-based trie-traverse searching scheme, where a multi-way tree structure is built up using symbols transformed from the resulted fuzzy keyword sets.
                   </p>
                     <p> Through rigorous security analysis, we show that our proposed solution is secure and privacypreserving, while correctly realizing the goal of fuzzy keyword search. Extensive experimental results demonstrate the efficiency of the proposed solution.
                      </p>
                <p>
                    <h3>Diagram</h3>
                    <img src="{{ URL::to('/') }}/fuzzy_diagram1.png" width="600px">
                </p>

                <b>Source Code availble on Github:</b>
                <a href="https://github.com/yousafsyed/fyp_fuzzy_keyword">https://github.com/yousafsyed/fyp_fuzzy_keyword</a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
