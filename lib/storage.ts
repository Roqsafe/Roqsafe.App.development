import { GetObjectCommand, PutObjectCommand, S3Client } from "@aws-sdk/client-s3";
import { getSignedUrl } from "@aws-sdk/s3-request-presigner";

const s3 = new S3Client({ forcePathStyle: true });
const bucket = "assets";

export async function storeText(key: string, body: string): Promise<string> {
    await s3.send(new PutObjectCommand({ Bucket: bucket, Key: key, Body: body }));
    const url = await getSignedUrl(
        s3,
        new GetObjectCommand({ Bucket: bucket, Key: key }),
        { expiresIn: 3600 },
    );
    return url;
}
