'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';
import { Headphones, Send, MapPin, Clock, Globe, CircleHelp, ChevronDown, Link as LinkIcon, ShieldCheck, Gauge, Home } from 'lucide-react';

export default function AboutPage() {
    const searchParams = useSearchParams();
    const isSent = searchParams.get('sent') === '1';
    const isError = searchParams.get('error') === '1';

    const [openFaq, setOpenFaq] = useState<number | null>(null);
    const [message, setMessage] = useState('');

    const topicMessages: Record<string, string> = {
        'IP Block / Unblock Request': 'Hello Roqsafe SVI team,\n\nI would like to request an unblock review for the IP address listed above. This IP is associated with my connection and I believe the block was triggered in error.\n\nPlease review and let me know what steps I need to take.\n\nThank you.',
        'Identity Verification': 'Hello Roqsafe SVI team,\n\nI need assistance verifying my identity on the SVI platform. I am unable to access certain sections and believe my account requires manual verification.\n\nPlease advise on the required steps.\n\nThank you.',
        'Account Access Issue': 'Hello Roqsafe SVI team,\n\nI am experiencing issues accessing my account. I am unable to log in and have tried resetting my credentials without success.\n\nCould you please look into this for me?\n\nThank you.',
        'Security Incident Report': 'Hello Roqsafe SVI Security Team,\n\nI am writing to report a potential security incident. I have noticed suspicious activity related to the IP address listed above and wanted to bring it to your attention immediately.\n\nPlease investigate at your earliest convenience.\n\nThank you.',
        'MySpeed / Network Tools': 'Hello Roqsafe SVI team,\n\nI need assistance with the MySpeed Network Monitor tool. I am encountering issues with the setup or configuration and would appreciate guidance.\n\nThank you.',
        'Other': 'Hello Roqsafe SVI team,\n\nI am reaching out regarding a general inquiry. Please see my details below and feel free to contact me at the email address provided.\n\nThank you.'
    };

    const handleTopicChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const topic = e.target.value;
        if (topic && topicMessages[topic]) {
            setMessage(topicMessages[topic]);
        }
    };

    return (
        <div style={{ background: '#fff' }}>
            <style jsx>{`
                .sp-hero { background: #fff; border-bottom: 1px solid var(--border); padding: 64px 24px 56px; }
                .sp-hero-inner { max-width: 900px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 48px; flex-wrap: wrap; }
                .sp-hero-badge { display: inline-flex; align-items: center; gap: 7px; background: #f4f0ff; border: 1px solid #d6c9f8; padding: 5px 14px; border-radius: 50px; margin-bottom: 20px; color: var(--eh-purple); font-size: 11px; font-weight: 700; letter-spacing: 0.09em; text-transform: uppercase; }
                .sp-hero h1 { font-family: 'Outfit', sans-serif; font-size: clamp(32px, 5vw, 52px); font-weight: 700; color: var(--svi-navy); letter-spacing: -0.02em; margin-bottom: 16px; line-height: 1.15; }
                .sp-hero p { font-size: 17px; color: var(--muted); line-height: 1.7; max-width: 520px; }
                
                .sp-content { max-width: 1100px; margin: 64px auto; padding: 0 24px 80px; display: grid; grid-template-columns: 1fr 380px; gap: 40px; }
                @media (max-width: 900px) { .sp-content { grid-template-columns: 1fr; } .sp-hero-inner { flex-direction: column; } }
                
                .sp-form-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 40px; box-shadow: 0 8px 30px rgba(0,0,0,0.05); }
                .sp-form-title { font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 700; color: var(--svi-navy); margin-bottom: 8px; }
                .sp-form-desc { font-size: 14px; color: var(--muted); margin-bottom: 28px; line-height: 1.6; }
                
                .sp-form-group { margin-bottom: 20px; }
                .sp-label { display: block; font-size: 12px; font-weight: 700; color: var(--svi-navy); margin-bottom: 7px; letter-spacing: 0.04em; text-transform: uppercase; }
                .sp-input, .sp-select, .sp-textarea { width: 100%; padding: 12px 16px; background: var(--input-bg); border: 1px solid transparent; border-radius: 8px; font-size: 14px; color: var(--body-text); font-family: 'Inter', sans-serif; outline: none; transition: all 0.2s; }
                .sp-input:focus, .sp-select:focus, .sp-textarea:focus { border-color: var(--eh-purple); background: #fff; box-shadow: 0 0 0 3px rgba(76,45,138,0.08); }
                .sp-textarea { min-height: 130px; resize: vertical; }
                
                .sp-submit { width: 100%; padding: 14px; background: var(--eh-purple); color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.25s; }
                .sp-submit:hover { background: #3b236b; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(76,45,138,0.3); }
                .sp-success { text-align: center; padding: 20px; background: var(--status-ok-bg); border-radius: 8px; color: var(--status-ok); font-weight: 600; font-size: 15px; margin-top: 16px; }
                .sp-error { text-align: center; padding: 20px; background: var(--status-danger-bg); border-radius: 8px; color: var(--status-danger); font-weight: 600; font-size: 15px; margin-top: 16px; }
                
                .sp-sidebar { display: flex; flex-direction: column; gap: 24px; }
                .sp-info-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; }
                .sp-info-title { font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700; color: var(--svi-navy); margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
                
                .sp-contact-row { display: flex; align-items: flex-start; gap: 14px; padding: 14px 0; border-bottom: 1px solid #f0f0f0; }
                .sp-contact-row:last-child { border-bottom: none; }
                .sp-contact-icon { width: 38px; height: 38px; border-radius: 10px; background: #f4f0ff; color: var(--eh-purple); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                .sp-contact-method { font-size: 12px; color: var(--muted); font-weight: 500; margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.05em; }
                .sp-contact-value { font-size: 14px; font-weight: 600; color: var(--svi-navy); }
                
                .sp-faq-item { border-bottom: 1px solid var(--border); padding: 16px 0; }
                .sp-faq-q { font-weight: 700; color: var(--svi-navy); font-size: 14px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 12px; user-select: none; }
                .sp-faq-a { font-size: 13px; color: var(--muted); line-height: 1.7; display: none; margin-top: 10px; }
                .sp-faq-item.open .sp-faq-a { display: block; }
                .sp-faq-chevron { transition: transform 0.25s; flex-shrink: 0; }
                .sp-faq-item.open .sp-faq-chevron { transform: rotate(180deg); }
                
                .quick-link { display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--input-bg); border-radius:8px; text-decoration:none; color:var(--svi-navy); font-size:14px; font-weight:600; transition:all 0.2s; }
                .quick-link:hover { background: #e2e8f0; }
            `}</style>

            <div className="sp-hero">
                <div className="sp-hero-inner">
                    <div>
                        <div className="sp-hero-badge">
                            <Headphones size={12} />
                            Roqsafe SVI Support
                        </div>
                        <h1>How can we<br />help you?</h1>
                        <p>Our security team is ready to assist with IP blocks, account verifications, and all platform-related issues. We typically respond within 24 hours.</p>
                    </div>
                </div>
            </div>

            <div className="sp-content">
                <div className="sp-form-card">
                    <div className="sp-form-title">Send a Support Request</div>
                    <div className="sp-form-desc">Fill in the form below and a Roqsafe SVI specialist will contact you. For urgent IP unblock requests, please mention your IP address.</div>

                    <form method="POST" action="/api/support">
                        <div className="sp-form-group">
                            <label className="sp-label" htmlFor="sp-name">Full Name</label>
                            <input className="sp-input" id="sp-name" name="sp_name" type="text" placeholder="Your name" required />
                        </div>
                        <div className="sp-form-group">
                            <label className="sp-label" htmlFor="sp-email">Email Address</label>
                            <input className="sp-input" id="sp-email" name="sp_email" type="email" placeholder="you@example.com" required />
                        </div>
                        <div className="sp-form-group">
                            <label className="sp-label" htmlFor="sp-topic">Topic</label>
                            <select className="sp-select" id="sp-topic" name="sp_topic" onChange={handleTopicChange}>
                                <option value="">Select a topic…</option>
                                <option value="IP Block / Unblock Request">IP Block / Unblock Request</option>
                                <option value="Identity Verification">Identity Verification</option>
                                <option value="Account Access Issue">Account Access Issue</option>
                                <option value="Security Incident Report">Security Incident Report</option>
                                <option value="MySpeed / Network Tools">MySpeed / Network Tools</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div className="sp-form-group">
                            <label className="sp-label" htmlFor="sp-ip">Your IP Address (optional)</label>
                            <input className="sp-input" id="sp-ip" name="sp_ip" type="text" />
                        </div>
                        <div className="sp-form-group">
                            <label className="sp-label" htmlFor="sp-message">Message</label>
                            <textarea className="sp-textarea" id="sp-message" name="sp_message" placeholder="Describe your issue in detail…" required value={message} onChange={(e) => setMessage(e.target.value)}></textarea>
                        </div>
                        <button type="submit" className="sp-submit">
                            <Send size={16} />
                            Send Request
                        </button>
                        
                        {isSent && <div className="sp-success">✅ Your request has been submitted. We&apos;ll be in touch within 24 hours.</div>}
                        {isError && <div className="sp-error">Mail delivery failed. Please email us directly at security@roqsafe.com.</div>}
                    </form>
                </div>

                <div className="sp-sidebar">
                    <div className="sp-info-card">
                        <div className="sp-info-title">
                            <MapPin size={20} color="var(--eh-purple)" />
                            Contact Details
                        </div>
                        <div className="sp-contact-row">
                            <div className="sp-contact-icon"><Clock size={18} /></div>
                            <div>
                                <div className="sp-contact-method">Response Time</div>
                                <div className="sp-contact-value">Within 24 hours</div>
                            </div>
                        </div>
                        <div className="sp-contact-row">
                            <div className="sp-contact-icon"><Globe size={18} /></div>
                            <div>
                                <div className="sp-contact-method">Headquarters</div>
                                <div className="sp-contact-value">Roqsafe LTD, Netherlands</div>
                            </div>
                        </div>
                    </div>

                    <div className="sp-info-card">
                        <div className="sp-info-title">
                            <CircleHelp size={20} color="var(--eh-purple)" />
                            Frequently Asked
                        </div>

                        {[
                            { q: 'My IP is blocked. What do I do?', a: 'Visit the Verification Portal and enter your IP address. If blocked, submit a deblock request or contact support directly.' },
                            { q: 'How long does an IP block last?', a: 'Soft blocks are typically reviewed within 48 hours. Permanent blocks require a manual review by our security team and direct written communication.' },
                            { q: 'What triggers an automatic block?', a: 'Our system automatically blocks IPs that exceed daily check limits, attempt access to restricted routes, or display patterns consistent with unauthorized scanning.' }
                        ].map((faq, idx) => (
                            <div key={idx} className={`sp-faq-item ${openFaq === idx ? 'open' : ''}`}>
                                <div className="sp-faq-q" onClick={() => setOpenFaq(openFaq === idx ? null : idx)}>
                                    {faq.q}
                                    <ChevronDown className="sp-faq-chevron" size={16} />
                                </div>
                                <div className="sp-faq-a">{faq.a}</div>
                            </div>
                        ))}
                    </div>

                    <div className="sp-info-card">
                        <div className="sp-info-title">
                            <LinkIcon size={20} color="var(--eh-purple)" />
                            Quick Links
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                            <Link href="/Verifi/data/user/default" className="quick-link">
                                <ShieldCheck size={16} color="var(--eh-purple)" />
                                IP Verification Portal
                            </Link>
                            <Link href="/myspeed" className="quick-link">
                                <Gauge size={16} color="var(--eh-purple)" />
                                MySpeed Monitor
                            </Link>
                            <Link href="/" className="quick-link">
                                <Home size={16} color="var(--eh-purple)" />
                                Back to Home
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
