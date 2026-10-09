import nodemailer from 'nodemailer';

export async function sendMail(to: string, subject: string, text: string, replyTo?: string) {
    const host = process.env.SMTP_HOST || 'mail.spacemail.com';
    const port = parseInt(process.env.SMTP_PORT || '465');
    const user = process.env.SMTP_USER || 'security@roqsafe.com';
    const pass = process.env.SMTP_PASS || '';

    const transporter = nodemailer.createTransport({
        host,
        port,
        secure: port === 465,
        auth: {
            user,
            pass,
        },
    });

    const mailOptions = {
        from: `"Roqsafe SVI" <${user}>`,
        to,
        subject,
        text,
        replyTo: replyTo || user,
    };

    try {
        await transporter.sendMail(mailOptions);
        return true;
    } catch (error) {
        console.error('[SVI MAIL] Error sending email:', error);
        return false;
    }
}
