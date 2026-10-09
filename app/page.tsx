import Link from 'next/link';
import { ShieldCheck, Gauge } from 'lucide-react';

export default function Home() {
    return (
        <div className="hp-wrap">
            {/* ── SYNCO HERO ───────────────────────────────────────────── */}
            <section className="synco-hero">
                <div className="synco-grid-overlay"></div>
                <div className="synco-hero-content">
                    <h1 className="synco-hero-heading">About Our<br />Studio</h1>
                    <p className="synco-hero-sub">We&apos;re a small team of designers and developers who build clean, functional websites for businesses of all sizes. No buzzwords, no fluff — just thoughtful design, solid code.</p>
                </div>
            </section>

            {/* ── SYNCO STATS ──────────────────────────────────────────── */}
            <section className="synco-stats-wrapper">
                <div className="synco-stats-container">
                    <div className="synco-stats-top">
                        <div className="synco-brand-tag">//<span>About Synco</span></div>
                        <h2 className="synco-stats-headline">Synco isn&apos;t just about change — we&apos;re setting new standards with bold creativity and thinking innovation.</h2>
                    </div>

                    <div className="synco-stats-grid">
                        {/* Row 1 */}
                        <div className="synco-block st-1 synco-block-left">
                            <span className="synco-num-tag">[01]</span>
                            <div className="synco-val-wrap">
                                <div className="synco-stat-val">50<span className="synco-stat-sign">+</span></div>
                                <div className="synco-stat-desc">Happy clients across various industries</div>
                            </div>
                            {/* Top-right cross for next block interaction */}
                            <div className="synco-cross tr"></div>
                        </div>
                        <div className="synco-block st-empty-1"></div>

                        {/* Row 2 */}
                        <div className="synco-block st-empty-2"></div>
                        <div className="synco-block st-2 synco-block-right">
                            <span className="synco-num-tag">[02]</span>
                            <div className="synco-val-wrap">
                                <div className="synco-stat-val">100<span className="synco-stat-sign">+</span></div>
                                <div className="synco-stat-desc">Successfully delivered high-quality projects</div>
                            </div>
                            <div className="synco-cross bl"></div>
                        </div>

                        {/* Row 3 */}
                        <div className="synco-block st-3 synco-block-left">
                            <span className="synco-num-tag">[03]</span>
                            <div className="synco-val-wrap">
                                <div className="synco-stat-val">98<span className="synco-stat-sign">%</span></div>
                                <div className="synco-stat-desc">Successfully delivered high- quality projects</div>
                            </div>
                            <div className="synco-cross tr"></div>
                        </div>
                        <div className="synco-block st-empty-3"></div>

                        {/* Row 4 */}
                        <div className="synco-block st-empty-4"></div>
                        <div className="synco-block st-4 synco-block-right">
                            <span className="synco-num-tag">[04]</span>
                            <div className="synco-val-wrap">
                                <div className="synco-stat-val">30<span className="synco-stat-sign">%</span></div>
                                <div className="synco-stat-desc">Increase in performance and usability</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* ── CTA BAR ────────────────────────────────────────────────── */}
            <div className="hp-cta-bar">
                <h2>Start verifying your connection today.</h2>
                <p>No account required — just enter your IP and get an instant security report.</p>
                <div className="hp-cta-row" style={{ justifyContent: 'center' }}>
                    <Link href="/Verifi/data/user/default" className="hp-btn-primary">
                        <ShieldCheck size={17} />
                        Check My IP Status
                    </Link>
                    <Link href="/myspeed" className="hp-btn-secondary">
                        <Gauge size={17} />
                        Run Speed Test
                    </Link>
                </div>
            </div>
        </div>
    );
}
