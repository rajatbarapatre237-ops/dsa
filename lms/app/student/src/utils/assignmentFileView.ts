export type AssignmentFileMode = 'image' | 'pdf' | 'document';

export function getAssignmentFileMode(documentName?: string | null): AssignmentFileMode {
  const name = String(documentName ?? '').toLowerCase();
  if (/\.(jpe?g|png|gif|webp|heic|heif)$/i.test(name)) return 'image';
  if (/\.pdf$/i.test(name)) return 'pdf';
  return 'document';
}

export function assignmentAuthFileUrl(
  apiBaseUrl: string,
  role: string,
  id: number,
  index: number,
): string {
  return `${apiBaseUrl}/${role}/assignments/${id}/file?index=${index}`;
}

export function assignmentPublicFileUrl(publicBaseUrl: string, id: number, index: number): string {
  return `${publicBaseUrl}/assignments/download/${id}?index=${index}`;
}

export async function fetchAuthenticatedBase64(uri: string, token: string): Promise<string> {
  const response = await fetch(uri, {
    headers: { Authorization: `Bearer ${token}`, Accept: '*/*' },
  });
  if (!response.ok) {
    throw new Error('Could not load file');
  }
  const blob = await response.blob();
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => {
      const result = String(reader.result ?? '');
      resolve(result.includes(',') ? result.split(',')[1] : result);
    };
    reader.onerror = () => reject(new Error('Could not read file'));
    reader.readAsDataURL(blob);
  });
}

export async function fetchPublicImageDataUri(uri: string): Promise<string> {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', uri);
    xhr.setRequestHeader('Accept', '*/*');
    xhr.responseType = 'blob';
    xhr.onload = () => {
      if (xhr.status < 200 || xhr.status >= 300) {
        reject(new Error(`HTTP ${xhr.status}`));
        return;
      }
      const reader = new FileReader();
      reader.onloadend = () => resolve(String(reader.result ?? ''));
      reader.onerror = () => reject(new Error('Could not read image'));
      reader.readAsDataURL(xhr.response);
    };
    xhr.onerror = () => reject(new Error('Network error'));
    xhr.send();
  });
}

export async function fetchImageDataUri(
  authUri: string,
  token: string | null,
  publicUri: string,
): Promise<string> {
  if (token) {
    try {
      return await fetchAuthenticatedImageDataUri(authUri, token);
    } catch {
      // Try public download URL next.
    }
  }

  return fetchPublicImageDataUri(publicUri);
}

export async function fetchAuthenticatedImageDataUri(uri: string, token: string): Promise<string> {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', uri);
    xhr.setRequestHeader('Authorization', `Bearer ${token}`);
    xhr.setRequestHeader('Accept', '*/*');
    xhr.responseType = 'blob';
    xhr.onload = () => {
      if (xhr.status < 200 || xhr.status >= 300) {
        reject(new Error(`HTTP ${xhr.status}`));
        return;
      }
      const reader = new FileReader();
      reader.onloadend = () => resolve(String(reader.result ?? ''));
      reader.onerror = () => reject(new Error('Could not read image'));
      reader.readAsDataURL(xhr.response);
    };
    xhr.onerror = () => reject(new Error('Network error'));
    xhr.send();
  });
}

export function buildPdfViewerHtml(base64: string): string {
  const data = JSON.stringify(base64);

  return `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=4.0" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <style>
    html, body { margin: 0; padding: 0; background: #f1f5f9; }
    #pages { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 12px 8px 24px; box-sizing: border-box; }
    canvas { max-width: 100%; height: auto !important; display: block; background: #fff; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.12); border-radius: 4px; }
    #error { padding: 24px; color: #64748b; font-family: -apple-system, sans-serif; text-align: center; }
  </style>
</head>
<body>
  <div id="pages"></div>
  <script>
    (async function () {
      try {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        const pdfData = atob(${data});
        const bytes = new Uint8Array(pdfData.length);
        for (let i = 0; i < pdfData.length; i++) bytes[i] = pdfData.charCodeAt(i);
        const pdf = await pdfjsLib.getDocument({ data: bytes }).promise;
        const wrap = document.getElementById('pages');
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
          const page = await pdf.getPage(pageNum);
          const viewport = page.getViewport({ scale: 1.35 });
          const canvas = document.createElement('canvas');
          canvas.width = viewport.width;
          canvas.height = viewport.height;
          wrap.appendChild(canvas);
          await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
        }
      } catch (e) {
        document.body.innerHTML = '<p id="error">Could not display PDF.</p>';
      }
    })();
  </script>
</body>
</html>`;
}
