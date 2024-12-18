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
        size: A4 portrait;
        /* B4 縦向き */
        margin: 0;
        /* 余白 */
        padding: 0;
    }

    @media print {
        body {
            padding: 0mm;
            margin: 0mm;
            /* これが無いと余分な余白が入る */
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

    .result-list {
        width: 100%;
        border-collapse: collapse;
    }

    .result-list th,
    .result-list td {
        border: 1px solid #ddd;
        padding: 8px;
        vertical-align: middle;
    }

    .result-list th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .polyline {
        display: inline-block;
        vertical-align: middle;
        margin-left: 10px;
    }

    .result-canvas {
        vertical-align: middle;
    }
</style>

@php
    $surveyTargetUser = $surveyAnswer->surveyTargetUser;
    $userName = $surveyTargetUser->post || $surveyTargetUser->team ? $surveyTargetUser->user->name : "";
@endphp

<div class="main-panel">
    <div class="content">
        <p class="back-link">
            <a class="arrow-right left" href="#" onclick="history.back(-1);return false;">戻る</a>
        </p>

        {{-- <div class="cover">
            <div class="subject">
                <span class="company-name">{{ $company->name }}</span>
                <span class="survey-title">{{ $survey->title }}</span>
            </div>
            <div class="info">
                <dl>
                    <dt>実施時期</dt>
                    <dd>{{ optional($surveyAnswer->completes_at)->format("Y年n月") }}</dd>
                    <dt>企業名</dt>
                    <dd>{{ $company->name }}</dd>
                    <dt>氏名</dt>
                    <dd>{{ $userName }}</dd>
                </dl>
            </div>
        </div> --}}

        <div>
            <div class="content_wrap">
                <h1 class="content_title">診断結果</h1>
                <p class="date">
                    受診日: {{ $surveyAnswer->completes_at ? $surveyAnswer->completes_at->format("Y/m/d") : "未受診" }}
                    <br><span>{{ $company->name }} / {{ $survey->title }} / {{ $userName }}</span>
                </p>
            </div>
            <div class="content_action save_wrap mb-2">
                <p class="patient_name font-weight-bold">
                    受診者:
                    @if ($surveyTargetUser->team)
                        {{ $surveyTargetUser->team }}&emsp;{{ $userName }}
                    @else
                        {{ $surveyTargetUser->user->email }}
                    @endif
                </p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card-body">
                    <table class="result-list" id="result-table">
                        <thead>
                            <tr>
                                <th class="text-center">Q</th>
                                <th class="widthtype01">設問内容</th>
                                <th>大分類</th>
                                <th>中分類</th>
                                <th>小分類</th>
                                <th class="widthtype02" style="width: 30%">回答</th>
                            </tr>
                        </thead>
                        <tbody class="graph_line">
                            @foreach ($surveyAnswerDetails as $questionId => $answers)
                                @php
                                    $answer = $answers->first();
                                @endphp
                                <tr class="no-link">
                                    <td class="text-center">{{ $answer->sort }}</td>
                                    <td>{{ $answer->question_text }}</td>
                                    <td>{{ $answer->major_category }}</td>
                                    <td>{{ $answer->medium_category }}</td>
                                    <td>{{ $answer->minor_category }}</td>
                                    <td @style(' word-break: break-word;')>
                                        @if ($answer->score !== null)
                                            {{ number_format($answer->score, 1) }}
                                            <div class="polyline">
                                                <canvas class="result-canvas" id="Q-{{ $questionId }}" width="200"
                                                    height="30"></canvas>
                                            </div>
                                        @else
                                            {!! nl2br(e($answer->text ?? "N/A")) !!}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.result-canvas').forEach(canvas => {
            const score = parseFloat(canvas.closest('td').textContent);
            if (!isNaN(score)) {
                drawGraph(canvas.id, score);
            }
        });
    });

    function drawGraph(canvasId, score) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;

        // 背景に目盛りを描画
        ctx.beginPath();
        for (let i = 0; i <= 5; i++) {
            const x = width * (i / 5);
            ctx.moveTo(x, 0);
            ctx.lineTo(x, height);
        }
        ctx.strokeStyle = '#e0e0e0';
        ctx.stroke();

        // スコアに基づいて線を描画
        ctx.beginPath();
        ctx.moveTo(0, height / 2);
        ctx.lineTo(width * (score / 5), height / 2);
        ctx.strokeStyle = 'blue';
        ctx.lineWidth = 2;
        ctx.stroke();

        // 点の描画
        ctx.beginPath();
        ctx.arc(width * (score / 5), height / 2, 4, 0, 2 * Math.PI);
        ctx.fillStyle = 'blue';
        ctx.fill();
    }
</script>
