'use client';

import { useState, useRef } from 'react';
import { Gauge as GaugeIcon, ArrowDown, ArrowUp, Activity, Play } from 'lucide-react';

export default function MySpeedPage() {
    const [testRunning, setTestRunning] = useState(false);
    const [gaugeVal, setGaugeVal] = useState<string>('–');
    const [gaugeColor, setGaugeColor] = useState('var(--eh-purple)');
    const [gaugeOffset, setGaugeOffset] = useState(534);
    
    const [dlVal, setDlVal] = useState<string>('–');
    const [ulVal, setUlVal] = useState<string>('–');
    const [pingVal, setPingVal] = useState<string>('–');
    
    const [phaseLabel, setPhaseLabel] = useState('Press "Start Test" to measure your connection');
    const [gaugeLbl, setGaugeLbl] = useState('Ready');
    
    const [activeMetric, setActiveMetric] = useState<string | null>(null);
    const [currentStep, setCurrentStep] = useState<string | null>(null);
    const [progressWidth, setProgressWidth] = useState('0%');
    
    const [showResults, setShowResults] = useState(false);
    const [results, setResults] = useState({ dl: '–', ul: '–', ping: '–', jitter: '–', rating: '' });

    const GAUGE_MAX = 200;
    const GAUGE_ARC = 440;

    const updateGauge = (mbps: number, color: string = 'var(--eh-purple)') => {
        const pct = Math.min(mbps / GAUGE_MAX, 1);
        const offset = 534 - pct * GAUGE_ARC;
        setGaugeOffset(offset);
        setGaugeColor(color);
        setGaugeVal(mbps > 0 ? mbps.toFixed(1) : '–');
    };

    const setStep = (step: string) => {
        setCurrentStep(step);
        const progress: Record<string, number> = { ping: 25, dl: 75, ul: 95, done: 100 };
        setProgressWidth(`${progress[step] || 0}%`);
    };

    const isStepDone = (step: string) => {
        const order = ['ping', 'dl', 'ul', 'done'];
        const curIdx = currentStep ? order.indexOf(currentStep) : -1;
        const targetIdx = order.indexOf(step);
        return targetIdx < curIdx;
    };

    const sleep = (ms: number) => new Promise(r => setTimeout(r, ms));

    const measurePing = async (n = 8) => {
        const pings: number[] = [];
        for (let i = 0; i < n; i++) {
            const t0 = performance.now();
            try { await fetch('https://1.1.1.1/cdn-cgi/trace?_=' + Date.now(), { method: 'GET', mode: 'no-cors', cache: 'no-store' }); } catch (e) { }
            pings.push(performance.now() - t0);
            await sleep(80);
        }
        pings.sort((a, b) => a - b);
        const t = pings.slice(1, -1);
        const avgPing = Math.round(t.reduce((a, b) => a + b, 0) / (t.length || 1));
        const jitter = Math.round(Math.max(...t) - Math.min(...t)) || 0;
        return { ping: avgPing, jitter };
    };

    const measureDownload = async () => {
        const jobs = [
            { url: 'https://speed.cloudflare.com/__down?bytes=1000000', bytes: 1e6 },
            { url: 'https://speed.cloudflare.com/__down?bytes=5000000', bytes: 5e6 },
            { url: 'https://speed.cloudflare.com/__down?bytes=10000000', bytes: 10e6 },
        ];
        const speeds: number[] = [];
        for (const { url, bytes } of jobs) {
            try {
                const t0 = performance.now();
                await (await fetch(url + '&_=' + Date.now(), { cache: 'no-store' })).arrayBuffer();
                const mbps = (bytes * 8) / ((performance.now() - t0) / 1000) / 1e6;
                speeds.push(mbps);
                updateGauge(mbps, 'var(--eh-purple)');
                setDlVal(mbps.toFixed(1));
            } catch (e) { speeds.push(0); }
        }
        speeds.sort((a, b) => a - b);
        return speeds[speeds.length - 1] || 0;
    };

    const measureUpload = async () => {
        const sizes = [500000, 2000000, 5000000];
        const speeds: number[] = [];
        for (const size of sizes) {
            try {
                const blob = new Blob([new Uint8Array(size)]);
                const t0 = performance.now();
                await fetch('https://speed.cloudflare.com/__up', { method: 'POST', body: blob, cache: 'no-store' });
                const mbps = (size * 8) / ((performance.now() - t0) / 1000) / 1e6;
                speeds.push(mbps);
                updateGauge(mbps, '#059669');
                setUlVal(mbps.toFixed(1));
            } catch (e) { speeds.push(0); }
        }
        speeds.sort((a, b) => a - b);
        return speeds[speeds.length - 1] || 0;
    };

    const getRating = (dl: number, ul: number, ping: number) => {
        if (dl > 100 && ul > 50 && ping < 20) return '🚀 Excellent — Ultra-fast connection';
        if (dl > 50 && ul > 20 && ping < 50) return '✅ Great — Fast and reliable';
        if (dl > 20 && ul > 10 && ping < 80) return '👍 Good — Suitable for most tasks';
        if (dl > 5 && ul > 3) return '⚡ Fair — Fine for everyday browsing';
        return '⚠️ Slow — May struggle with HD streaming';
    };

    const startTest = async () => {
        if (testRunning) return;
        setTestRunning(true);
        setShowResults(false);
        setDlVal('–'); setUlVal('–'); setPingVal('–');
        updateGauge(0); setStep('ping');

        // Ping
        setActiveMetric('m-ping');
        setPhaseLabel('Measuring latency…');
        setGaugeLbl('Ping');
        const { ping, jitter } = await measurePing();
        setPingVal(ping.toString());
        setGaugeVal(ping.toString());
        setGaugeLbl('ms');
        updateGauge(0); await sleep(350);

        // Download
        setStep('dl'); setActiveMetric('m-dl');
        setPhaseLabel('Measuring download speed…');
        setGaugeLbl('Download');
        const dl = await measureDownload();
        setDlVal(dl.toFixed(1));
        await sleep(350);

        // Upload
        setStep('ul'); setActiveMetric('m-ul');
        setPhaseLabel('Measuring upload speed…');
        setGaugeLbl('Upload');
        updateGauge(0, '#059669');
        const ul = await measureUpload();
        setUlVal(ul.toFixed(1));
        await sleep(350);

        // Done
        setStep('done'); setActiveMetric(null);
        updateGauge(dl, 'var(--eh-purple)');
        setGaugeLbl('Done ✓');
        setPhaseLabel('Test complete!');

        setResults({
            dl: dl.toFixed(1),
            ul: ul.toFixed(1),
            ping: ping.toString(),
            jitter: jitter.toString(),
            rating: getRating(dl, ul, ping)
        });
        setShowResults(true);
        setTestRunning(false);
    };

    return (
        <div style={{ background: '#fff', padding: '0 0 80px' }}>
            <style jsx>{`
                .ms-hero { background: #fff; border-bottom: 1px solid var(--border); padding: 56px 24px 48px; text-align: center; }
                .ms-badge { display: inline-flex; align-items: center; gap: 7px; background: #f4f0ff; border: 1px solid #ddd6fe; color: var(--eh-purple); padding: 5px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; letter-spacing: 0.09em; text-transform: uppercase; margin-bottom: 18px; }
                .ms-hero h1 { font-family: 'Outfit', sans-serif; font-size: clamp(30px, 5vw, 52px); font-weight: 800; color: var(--svi-navy); letter-spacing: -0.03em; margin-bottom: 10px; line-height: 1.1; }
                .ms-hero h1 span { color: var(--eh-purple); }
                .ms-hero p { color: var(--muted); font-size: 16px; }
                
                .ms-panel { max-width: 700px; margin: 48px auto 0; padding: 0 24px; }
                
                .ms-gauge-wrap { display: flex; flex-direction: column; align-items: center; gap: 6px; margin-bottom: 32px; }
                .ms-gauge { position: relative; width: 220px; height: 220px; }
                .ms-gauge svg { width: 100%; height: 100%; transform: rotate(-220deg); }
                .ms-gauge-track { fill: none; stroke: var(--input-bg); stroke-width: 13; stroke-linecap: round; }
                .ms-gauge-fill { fill: none; stroke-width: 13; stroke-linecap: round; stroke-dasharray: 534; transition: stroke-dashoffset 0.35s ease, stroke 0.35s ease; filter: drop-shadow(0 0 6px rgba(76,45,138,0.4)); }
                .ms-gauge-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none; }
                .ms-gauge-val { font-family: 'Outfit', sans-serif; font-size: 48px; font-weight: 800; color: var(--svi-navy); line-height: 1; }
                .ms-gauge-unit { font-size: 13px; color: var(--muted); font-weight: 500; margin-top: 2px; }
                .ms-gauge-lbl { font-size: 11px; color: var(--eh-purple); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 4px; }
                .ms-phase-label { font-size: 13px; color: var(--muted); height: 18px; margin-top: 4px; }
                
                .ms-metrics { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 24px; }
                .ms-metric { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 14px; text-align: center; transition: border-color 0.25s, box-shadow 0.25s; }
                .ms-metric.active { border-color: var(--eh-purple); box-shadow: 0 0 0 3px rgba(76,45,138,0.08); }
                .ms-metric-icon { width: 34px; height: 34px; border-radius: 9px; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; }
                .ms-metric-icon.dl { background: #f4f0ff; color: var(--eh-purple); }
                .ms-metric-icon.ul { background: #ecfdf5; color: #059669; }
                .ms-metric-icon.pi { background: #eff6ff; color: #2563eb; }
                .ms-metric-val { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 800; color: var(--svi-navy); line-height: 1; }
                .ms-metric-unit { font-size: 10px; color: var(--muted); font-weight: 600; margin-top: 2px; letter-spacing: 0.04em; }
                .ms-metric-label { font-size: 10px; color: var(--muted); margin-top: 5px; text-transform: uppercase; letter-spacing: 0.07em; }
                
                .ms-progress-wrap { margin-bottom: 24px; }
                .ms-progress-bar { width: 100%; height: 3px; background: var(--input-bg); border-radius: 4px; overflow: hidden; }
                .ms-progress-fill { height: 100%; background: linear-gradient(90deg, var(--eh-purple), #a78bfa); border-radius: 4px; transition: width 0.4s ease; }
                .ms-progress-labels { display: flex; justify-content: space-between; font-size: 11px; color: var(--muted); margin-top: 8px; }
                .ms-progress-step { transition: color 0.3s; }
                .ms-progress-step.done { color: var(--eh-purple); }
                .ms-progress-step.active { color: var(--svi-navy); font-weight: 700; }
                
                .ms-start-btn { width: 100%; padding: 16px; background: var(--eh-purple); color: #fff; border: none; border-radius: 10px; font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 700; cursor: pointer; transition: all 0.25s; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 6px 24px rgba(76,45,138,0.3); }
                .ms-start-btn:hover:not(:disabled) { background: #3b236b; transform: translateY(-2px); box-shadow: 0 10px 32px rgba(76,45,138,0.45); }
                .ms-start-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
                
                .ms-results { display: none; background: transparent; border: none; padding: 32px 0 0 0; margin-top: 24px; }
                .ms-results.show { display: flex; flex-direction: column; gap: 32px; align-items: center; }
                .ms-result-item { text-align: center; }
                .ms-result-val { font-family: 'Outfit', sans-serif; font-size: 48px; font-weight: 800; line-height: 1; color: var(--svi-navy); }
                .ms-result-label { font-size: 13px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.12em; margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 6px; }
                .ms-rating { text-align: center; margin-top: 8px; font-size: 17px; font-weight: 700; color: var(--svi-navy); }
            `}</style>

            <div className="ms-hero">
                <div className="ms-badge">
                    <GaugeIcon size={12} />
                    Network Intelligence
                </div>
                <h1>Test Your <span>Internet</span> Speed</h1>
                <p>Powered by Cloudflare&apos;s global edge network — accurate download, upload & ping in seconds.</p>
            </div>

            <div className="ms-panel">
                <div className="ms-gauge-wrap">
                    <div className="ms-gauge">
                        <svg viewBox="0 0 200 200">
                            <circle className="ms-gauge-track" cx="100" cy="100" r="85" strokeDasharray="534" strokeDashoffset="93" />
                            <circle className="ms-gauge-fill" style={{ strokeDashoffset: gaugeOffset, stroke: gaugeColor }} cx="100" cy="100" r="85" />
                        </svg>
                        <div className="ms-gauge-center">
                            <div className="ms-gauge-val">{gaugeVal}</div>
                            <div className="ms-gauge-unit">Mbps</div>
                            <div className="ms-gauge-lbl">{gaugeLbl}</div>
                        </div>
                    </div>
                    <div className="ms-phase-label">{phaseLabel}</div>
                </div>

                <div className="ms-metrics">
                    <div className={`ms-metric ${activeMetric === 'm-dl' ? 'active' : ''}`}>
                        <div className="ms-metric-icon dl"><ArrowDown size={17} /></div>
                        <div className="ms-metric-val">{dlVal}</div>
                        <div className="ms-metric-unit">Mbps</div>
                        <div className="ms-metric-label">Download</div>
                    </div>
                    <div className={`ms-metric ${activeMetric === 'm-ul' ? 'active' : ''}`}>
                        <div className="ms-metric-icon ul"><ArrowUp size={17} /></div>
                        <div className="ms-metric-val">{ulVal}</div>
                        <div className="ms-metric-unit">Mbps</div>
                        <div className="ms-metric-label">Upload</div>
                    </div>
                    <div className={`ms-metric ${activeMetric === 'm-ping' ? 'active' : ''}`}>
                        <div className="ms-metric-icon pi"><Activity size={17} /></div>
                        <div className="ms-metric-val">{pingVal}</div>
                        <div className="ms-metric-unit">ms</div>
                        <div className="ms-metric-label">Ping</div>
                    </div>
                </div>

                <div className="ms-progress-wrap">
                    <div className="ms-progress-bar">
                        <div className="ms-progress-fill" style={{ width: progressWidth }}></div>
                    </div>
                    <div className="ms-progress-labels">
                        {['ping', 'dl', 'ul', 'done'].map((step, i) => (
                            <span key={step} className={`ms-progress-step ${isStepDone(step) ? 'done' : currentStep === step ? 'active' : ''}`}>
                                {step === 'ping' ? 'Ping' : step === 'dl' ? 'Download' : step === 'ul' ? 'Upload' : 'Complete'}
                            </span>
                        ))}
                    </div>
                </div>

                <button className="ms-start-btn" onClick={startTest} disabled={testRunning}>
                    <Play size={19} />
                    <span>{testRunning ? 'Testing…' : showResults ? 'Test Again' : 'Start Test'}</span>
                </button>

                <div className={`ms-results ${showResults ? 'show' : ''}`}>
                    <div className="ms-result-item">
                        <div className="ms-result-val">{results.dl}</div>
                        <div className="ms-result-label"><span>⬇</span> DOWNLOAD MBPS</div>
                    </div>
                    <div className="ms-result-item">
                        <div className="ms-result-val">{results.ul}</div>
                        <div className="ms-result-label"><span>⬆</span> UPLOAD MBPS</div>
                    </div>
                    <div className="ms-result-item">
                        <div className="ms-result-val">{results.ping}</div>
                        <div className="ms-result-label"><span>🏓</span> PING MS</div>
                    </div>
                    <div className="ms-result-item">
                        <div className="ms-result-val">{results.jitter}</div>
                        <div className="ms-result-label"><span>〰</span> JITTER MS</div>
                    </div>
                    <div className="ms-rating">{results.rating}</div>
                </div>
            </div>
        </div>
    );
}
