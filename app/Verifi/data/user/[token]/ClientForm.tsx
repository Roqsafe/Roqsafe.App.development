'use client';

import { useState } from 'react';
import Swal from 'sweetalert2';
import { Lock } from 'lucide-react';
import Link from 'next/link';

export default function ClientForm({
    isBlocked,
    showIpTool,
    content
}: {
    isBlocked: boolean;
    showIpTool: boolean;
    content: Record<string, string>;
}) {
    const [ip, setIp] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [result, setResult] = useState<{ status: string; html: string } | null>(null);

    const handleDeblock = async (lastIp: string) => {
        setIsLoading(true);
        try {
            const res = await fetch('/api/deblock', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ip: lastIp })
            });
            const data = await res.json();
            
            Swal.fire({
                title: data.status === 'success' ? 'Debloc Requested' : 'Notice',
                html: data.message || 'Done.',
                icon: data.status === 'success' ? 'success' : 'info',
                confirmButtonColor: 'var(--eh-purple)'
            });
            setResult({ status: data.status, html: data.message || 'Done.' });
        } catch (err) {
            setResult({ status: 'error', html: 'Network error while submitting deblock request.' });
        } finally {
            setIsLoading(false);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const trimmedIp = ip.trim();
        if (!trimmedIp) return;
        
        setIsLoading(true);
        setResult(null);
        try {
            const res = await fetch('/api/block_check', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ip: trimmedIp })
            });
            const data = await res.json();
            
            const html = data.message || 'Unknown response.';
            const needsDeblockBtn = data.status === 'soft' && !data.deblock_requested;
            
            let targetIcon: 'warning' | 'success' | 'error' | 'info' | 'question' = 'warning';
            let targetTitle = 'Action Required';
            if (data.status === 'not_found' || data.status === 'deblocked') { targetIcon = 'success'; targetTitle = 'Verified'; }
            if (data.status === 'error') { targetIcon = 'error'; targetTitle = 'Error'; }

            Swal.fire({
                title: targetTitle,
                html: html + (needsDeblockBtn ? '<br><button class="deblock-btn" id="deblock-btn-swal" style="margin-top:20px;" type="button">Request Deblock</button>' : ''),
                icon: targetIcon,
                confirmButtonColor: 'var(--eh-purple)',
                didOpen: () => {
                    const pb = Swal.getPopup()?.querySelector('#deblock-btn-swal');
                    if (pb) pb.addEventListener('click', () => { Swal.close(); handleDeblock(trimmedIp); });
                }
            });

            setResult({ status: data.status, html });
        } catch (err) {
            setResult({ status: 'error', html: 'A network error occurred. Please try again.' });
        } finally {
            setIsLoading(false);
        }
    };

    if (!showIpTool) return null;

    if (isBlocked) {
        return (
            <div className="ip-form" style={{ background: 'var(--status-danger-bg)', padding: '20px 24px', borderRadius: '8px', border: '1px dashed var(--status-danger)', display: 'flex', flexDirection: 'column', gap: '14px', marginTop: '24px' }}>
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '16px', flexWrap: 'wrap' }}>
                    <span style={{ color: 'var(--status-danger)', fontWeight: 600, fontSize: '15px', display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <Lock size={18} />
                        Self-Verification Suspended
                    </span>
                    <Link href="/About" className="ip-btn" style={{ textDecoration: 'none', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', background: 'var(--status-danger)' }}>Contact Support</Link>
                </div>
                <p style={{ fontSize: '13px', color: 'var(--status-danger)', margin: 0, lineHeight: 1.5, opacity: 0.85 }}>Your network segment is currently restricted. Manual IP verification tools and self-service checks are disabled while your address remains blocked.</p>
            </div>
        );
    }

    return (
        <>
            <style jsx>{`
                .ip-form { margin: 28px 0 16px; display: flex; flex-direction: column; gap: 10px; max-width: 420px; }
                .ip-row { display: flex; gap: 8px; }
                .ip-input { flex: 1; background: var(--input-bg); border: none; border-radius: 6px; padding: 14px 20px; font-family: 'Source Serif 4', serif; font-size: 15px; color: var(--body-text); outline: none; transition: background 0.2s; }
                .ip-input:focus { background: #dddfe4; }
                .ip-btn { background: var(--svi-navy); color: #fff; border: none; border-radius: 6px; padding: 0 22px; font-family: 'Source Serif 4', serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s, transform 0.1s; white-space: nowrap; }
                .ip-btn:hover:not(:disabled) { background: var(--svi-navy-light); }
                .ip-btn:disabled { opacity: 0.7; cursor: not-allowed; }
                .ip-hint { font-size: 11px; color: var(--muted); letter-spacing: 0.03em; text-align: center; }
                .result-box { border-radius: 8px; padding: 14px 18px; font-size: 14px; line-height: 1.65; max-width: 420px; margin-top: 4px; }
                .result-box.not_found { background: var(--status-ok-bg); color: var(--status-ok); }
                .result-box.deblocked { background: var(--status-info-bg); color: var(--status-info); }
                .result-box.soft { background: var(--status-warn-bg); color: var(--status-warn); }
                .result-box.permanent { background: var(--status-danger-bg); color: var(--status-danger); }
                .result-box.pending { background: var(--status-warn-bg); color: var(--status-warn); }
                .result-box.ok { background: var(--status-ok-bg); color: var(--status-ok); }
                .result-box.error { background: var(--status-danger-bg); color: var(--status-danger); }
                .spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid currentColor; border-top-color: transparent; border-radius: 50%; animation: spin 0.7s linear infinite; vertical-align: middle; }
                @keyframes spin { to { transform: rotate(360deg); } }
                .deblock-btn { display: inline-block; margin-top: 10px; background: var(--status-warn); color: #fff; border: none; border-radius: 5px; padding: 8px 18px; font-family: 'Source Serif 4', serif; font-size: 13px; font-weight: 600; cursor: pointer; }
            `}</style>
            <form className="ip-form" onSubmit={handleSubmit} autoComplete="off" noValidate>
                <div className="ip-row">
                    <input
                        className="ip-input"
                        type="text"
                        value={ip}
                        onChange={(e) => setIp(e.target.value)}
                        placeholder={content['ip_input_placeholder'] || 'type your ip in for deblock'}
                        maxLength={45}
                    />
                    <button className="ip-btn" type="submit" disabled={isLoading}>
                        {isLoading ? <span className="spinner"></span> : (content['ip_submit_btn'] || 'Check IP')}
                    </button>
                </div>
                <span className="ip-hint">Check if the unblock works for you</span>
                {result && (
                    <div className={`result-box ${result.status}`}>
                        <div dangerouslySetInnerHTML={{ __html: result.html }}></div>
                        {result.status === 'soft' && (
                            <button className="deblock-btn" type="button" onClick={() => handleDeblock(ip)}>
                                Request Deblock
                            </button>
                        )}
                    </div>
                )}
            </form>
        </>
    );
}
