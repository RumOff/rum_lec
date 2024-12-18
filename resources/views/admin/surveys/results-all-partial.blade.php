<style>
    .cover {
        overflow: hidden;
        background-color: #fff;
        padding: 40px;
        margin-bottom: 30px;
        height: 1360px;
        position: relative;
        page-break-after: always;
    }
    .cover .subject {
        background-color: #FDD000;
        padding: 24px;
        color: #000;
        font-size: 16px;
        font-weight: bold;
        position: relative;
        top: 30%;
    }
    .cover .subject .company-name {
        margin-right: 12px;
    }
    .cover .info {
        position: absolute;
        right: 5%;
        bottom: 10%;
        font-size: 16px;
        line-height: 2;
    }
    .cover .info dt,
    .cover .info dd {
        border-bottom: solid 1px #333;
    }
    .cover .info dl {
        display: flex;
        flex-wrap: wrap;
    }
    .cover .info dl dt {
        width: 40%;
    }
    .cover .info dl dd {
        width: 60%;
    }
    .result-list td.text-question {
        padding: 24px;
        font-weight: bold;
    }


    @page {
        size: A4 portrait; /* B4 横向き */
        margin: 0; /* 余白 */
        padding: 0;
    }
    @media print {
        body{
            padding: 0mm;
            margin: 0mm;   /* これが無いと余分な余白が入る */
            background-color: #fff;
        }

        .back-link {
            display: none;
        }
        .main-panel,
        .main-panel>.content {
            width: 99%;
            padding: 4px;
            margin: 4px;
            {{-- background-color: #fff; --}}
        }
    }
</style>


<div class="main-panel">
    <div class="content">

        <p class="back-link">
            <a class="arrow-right left" href="#" onclick="history.back(-1);return false;">戻る</a>
        </p>

        <div class="cover">
            <div class="subject">
                <span class="company-name">{{ $company->name }}</span>
                <span class="survey-title">{{ $survey->title }}</span>
            </div>
            <div class="info">
                <dl>
                    <dt>実施時期</dt>
                    <dd>{{ optional($survey->expires_at)->format('Y年n月') }}</dd>

                    <dt>企業名</dt>
                    <dd>{{ $company->name }}</dd>
                </dl>
            </div>
        </div>

        <div>

            <div class="content_wrap">
                <h1 class="content_title">全体診断結果</h1>
            </div>
            <div class="content_action save_wrap mb-2">
                <p class="patient_name font-weight-bold">
                    {{ $company->name }} {{ $survey->title }} 全体診断結果
                </p>
            </div>

        </div>

        <!--list-->
        <div class="row mb-5">
            <div class="col-md-12">

                <div class="card-body">
                    <!--スマホ対応にて追加　card-body-->
                    <table class="result-list" id="result-table">
                        <tbody class="graph_line">
                            <!--タイトル-->
                            <tr>
                                @if ($hasCategory)
                                    <th colspan="2"></th>
                                @endif
                                <th>Q</th>
                                <th class="widthtype01">行動項目</th>
                                <th class="text-center">
                                    被評価者
                                </th>

                                {{-- 評価者の列を作成 --}}
                                @foreach($otherSurveyAnswerDetails as $post => $otherAnswer)
                                    <th class="text-center">
                                        {{ $post }}
                                        <br><small>(GAP)</small>
                                    </th>
                                @endforeach
                                {{-- // 評価者の列を作成 --}}

                                <th class="widthtype02" style="width: 200px">
                                    <div class="score-label">被評価者<span class="score_1_line"></span></div>
                                    @foreach($otherSurveyAnswerDetails as $post => $otherAnswer)
                                        <div class="score-label">
                                            {{ $post }}
                                            <span class="score_{{ $loop->index + 2 }}_line"></span>
                                        </div>
                                    @endforeach
                                    <div class="result-score" style="position:relative">
                                        <span>1</span>
                                        <span>2</span>
                                        <span>3</span>
                                        <span>4</span>
                                        <span>5</span>
                                    </div>
                                </th>
                            </tr>

                            {{-- categoryごとにloop --}}
                            @foreach ($questions as $category)
                                @if ($hasCategory)
                                    <tr>
                                        {{-- rowspanのサイズを合わせるために +major_category数 --}}
                                        <th class="tate" rowspan="{{ $category['count'] + count($category['major_category']) }}">
                                            {{ $category['name'] }}
                                        </th>
                                        {{-- rowspanの仕組みで +1 --}}
                                        <th class="tate" rowspan="{{ $category['major_category'][0]['count'] + 1 }}">
                                            {{ $category['major_category'][0]['name'] }}
                                        </th>

                                    </tr>
                                @endif

                                {{-- tableの仕組みで最初だけ切り出し --}}
                                @foreach($category['major_category'][0]['items'] as $item)
                                    @php
                                        $targetAnswerScore = $targetSurveyAnswerDetails->get($item->id)->avg('score');
                                    @endphp

                                    <tr>
                                        <td>{{ $item->sort }}</td>
                                        <td class="question-column">{{ $item->question }}</td>
                                        <td>{{ $targetAnswerScore ? number_format($targetAnswerScore, 2) : null }}</td>

                                        @foreach($otherSurveyAnswerDetails as $otherAnswer)
                                            @php
                                                $otherAnswerScore = $otherAnswer->get($item->id)->avg('score');
                                                $diffScore = $otherAnswerScore - $targetAnswerScore;
                                            @endphp
                                            <td class="score">
                                                @if (! is_null($otherAnswerScore))
                                                    {{ number_format($otherAnswerScore, 2) }}<br>
                                                    <small>({{ number_format($diffScore, 2) }})</small>
                                                @endif
                                            </td>
                                        @endforeach

                                        <td>
                                            <div class="polyline">
                                                <canvas id="Q-{{ $item->id }}" class="result-canvas" width="180px" height="80px"></canvas>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                {{-- // tableの仕組みで最初だけ切り出し --}}

                                @foreach($category['major_category'] as $key => $feature)
                                    {{-- tableの仕組みで最初だけ切り出したので最初のfeatureはここではスキップ --}}
                                    @if ($key !== 0)
                                        <tr>
                                            <th class="tate" rowspan="{{ $feature['count'] + 1 }}">{{ $feature['name'] }}</th>
                                        </tr>
                                        @foreach($feature['items'] as $item)
                                            @php
                                                $targetAnswerScore = $targetSurveyAnswerDetails->get($item->id)->avg('score');
                                            @endphp

                                            <tr>
                                                <td>{{ $item->sort }}</td>
                                                <td class="question-column">{{ $item->question }}</td>
                                                <td>{{ $targetAnswerScore ? number_format($targetAnswerScore, 2) : null }}</td>

                                                @foreach($otherSurveyAnswerDetails as $otherAnswer)
                                                    @php
                                                        $otherAnswerScore = $otherAnswer->get($item->id)->avg('score');
                                                        $diffScore = $otherAnswerScore - $targetAnswerScore;
                                                    @endphp
                                                    <td class="score">
                                                        @if (! is_null($otherAnswerScore))
                                                            {{ number_format($otherAnswerScore, 2) }}<br>
                                                            <small>({{ number_format($diffScore, 2) }})</small>
                                                        @endif
                                                    </td>
                                                @endforeach

                                                <td class="polyline">
                                                    <div class="polyline">
                                                        <canvas id="Q-{{ $item->id }}" class="result-canvas" width="180px" height="80px"></canvas>
                                                    </div>
                                            </tr>
                                        @endforeach
                                    @endif
                                    {{-- // tableの仕組みで最初だけ切り出したので最初のfeatureはここではスキップ --}}
                                @endforeach
                            @endforeach
                            {{-- // categoryごとにloop --}}

                        </tbody>
                    </table>

                </div>
                <!--スマホ対応にて追加　card-body-->

            </div>
        </div>

    </div>
</div>

<script>

    @foreach ($questions as $category)
        @foreach($category['major_category'] as $key => $feature)
            @foreach($feature['items'] as $item)
                const data{{ $item->id }} = {
                    labels: [
                        '',
                        @foreach($otherSurveyAnswerDetails as $otherAnswer)
                            '',
                        @endforeach
                    ],
                    datasets: [
                        {
                            label: '',
                            data: [
                                {{ $targetSurveyAnswerDetails->get($item->id)->avg('score') }},
                                @foreach($otherSurveyAnswerDetails as $otherAnswer)
                                    {{ $otherAnswer->get($item->id)->avg('score') }},
                                @endforeach
                            ],
                            borderColor: [
                                '#FDD000',
                                '#13cfdd',
                                '#bb3500',
                                '#002fbb',
                                '#b200bb',
                            ],
                            backgroundColor: [
                                '#FDD000',
                                '#13cfdd',
                                '#bb3500',
                                '#002fbb',
                                '#b200bb',
                            ],

                        },
                        {{-- {
                            laebl: '',
                            data: [1 , 2, 3, 4, 5],
                            borderColor: 'transparent',
                            backgroundColor: 'transparent',
                        } --}}
                    ]
                };

                const config{{ $item->id }} = {
                    type: 'bar',
                    data: data{{ $item->id }},
                    options: {
                        responsive: true,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                                }
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 0,
                                bottom: 0
                            }
                        },
                        scales: {
                            x: {
                                min: 0,
                                max: 5,
                                stepSize: 1,
                                border: {
                                    display: false
                                },
                                ticks: {
                                    display: false,
                                    max: 5,
                                    min: 1,
                                    stepSize: 1
                                },
                                grid: {
                                    display: false
                                },
                                categoryPercentage: 0.1
                            },
                            y: {
                                border: {
                                    display: false
                                },
                                ticks: {
                                    display: false,
                                },
                                grid: {
                                    display: false
                                }
                            },
                        }
                    }
                };

                const ctx{{ $item->id }} = document.getElementById('Q-{{ $item->id }}');
                new Chart(ctx{{ $item->id }}, config{{ $item->id }});

            @endforeach
        @endforeach
    @endforeach
</script>
