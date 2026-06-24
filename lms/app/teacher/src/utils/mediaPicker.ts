import { Alert, PermissionsAndroid, Platform } from 'react-native';
import {
  launchCamera,
  launchImageLibrary,
  type Asset,
  type ImagePickerResponse,
} from 'react-native-image-picker';

export type PickedFile = {
  uri: string;
  name: string;
  type: string;
  key: string;
  base64?: string;
};

function showPickerUnavailable() {
  Alert.alert(
    'Photo picker unavailable',
    'Rebuild the app after installing native dependencies (run pod install in ios, then rebuild).',
  );
}

function assetToFile(asset: Asset, fallbackPrefix: string): PickedFile | null {
  if (!asset.uri) return null;
  const type = asset.type === 'image/jpg' ? 'image/jpeg' : (asset.type ?? 'image/jpeg');
  let name = asset.fileName ?? `${fallbackPrefix}-${Date.now()}.jpg`;
  if (!/\.\w{2,5}$/i.test(name)) {
    name = `${name}.${extensionForMime(type)}`;
  }
  return {
    uri: asset.uri,
    name,
    type,
    base64: asset.base64 ?? undefined,
    key: `${asset.uri}-${name}-${Date.now()}-${Math.random()}`,
  };
}

function extensionForMime(type: string): string {
  if (type.includes('pdf')) return 'pdf';
  if (type.includes('png')) return 'png';
  if (type.includes('gif')) return 'gif';
  if (type.includes('webp')) return 'webp';
  if (type.includes('heic')) return 'heic';
  if (type.includes('heif')) return 'heif';
  if (type.includes('jpeg') || type.includes('jpg')) return 'jpg';
  if (type.includes('plain')) return 'txt';
  if (type.includes('msword')) return 'doc';
  if (type.includes('wordprocessingml')) return 'docx';
  return 'jpg';
}

function mimeForExtension(name: string): string | null {
  const match = name.toLowerCase().match(/\.([a-z0-9]+)$/);
  if (!match) return null;
  switch (match[1]) {
    case 'jpg':
    case 'jpeg':
      return 'image/jpeg';
    case 'png':
      return 'image/png';
    case 'gif':
      return 'image/gif';
    case 'webp':
      return 'image/webp';
    case 'heic':
      return 'image/heic';
    case 'heif':
      return 'image/heif';
    case 'pdf':
      return 'application/pdf';
    case 'txt':
      return 'text/plain';
    case 'doc':
      return 'application/msword';
    case 'docx':
      return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    default:
      return null;
  }
}

export function normalizeUploadFile(file: PickedFile): PickedFile {
  let { uri, name, type } = file;
  if (!uri.includes('://') && uri.startsWith('/')) {
    uri = `file://${uri}`;
  }
  if (type === 'image/jpg') type = 'image/jpeg';

  const hasExtension = /\.\w{2,5}$/i.test(name);
  if (!hasExtension) {
    const fallbackType =
      !type || type === 'application/octet-stream' ? inferMimeType(name, type) : type;
    name = `${name}.${extensionForMime(fallbackType)}`;
    if (!type || type === 'application/octet-stream') {
      type = fallbackType;
    }
  } else if (!type || type === 'application/octet-stream') {
    type = mimeForExtension(name) ?? type;
  }

  if (!type || type === 'application/octet-stream') {
    type = mimeForExtension(name) ?? inferMimeType(name, type);
  }

  return { ...file, uri, name, type };
}

function inferMimeType(name: string, type?: string | null): string {
  const fromName = mimeForExtension(name);
  if (fromName) return fromName;
  const hint = String(type ?? '').toLowerCase();
  if (hint.includes('pdf')) return 'application/pdf';
  if (hint.includes('png')) return 'image/png';
  if (hint.includes('jpeg') || hint.includes('jpg')) return 'image/jpeg';
  if (hint.includes('gif')) return 'image/gif';
  if (hint.includes('webp')) return 'image/webp';
  if (hint.includes('plain') || hint.includes('text')) return 'text/plain';
  return 'application/octet-stream';
}

function normalizeFileUri(uri: string): string {
  if (!uri) return uri;
  if (!uri.includes('://') && uri.startsWith('/')) {
    return `file://${uri}`;
  }
  return uri;
}

async function blobToBase64(blob: Blob): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onloadend = () => {
      const result = String(reader.result ?? '');
      resolve(result.includes(',') ? result.split(',')[1] : result);
    };
    reader.onerror = () => reject(new Error('Could not read file'));
    reader.readAsDataURL(blob);
  });
}

async function readFileBase64XHR(uri: string): Promise<string> {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.onload = () => {
      if (xhr.status !== 0 && xhr.status !== 200) {
        reject(new Error('Could not read file'));
        return;
      }
      const reader = new FileReader();
      reader.onloadend = () => {
        const result = String(reader.result ?? '');
        resolve(result.includes(',') ? result.split(',')[1] : result);
      };
      reader.onerror = () => reject(new Error('Could not read file'));
      reader.readAsDataURL(xhr.response);
    };
    xhr.onerror = () => reject(new Error('Could not read file'));
    xhr.responseType = 'blob';
    xhr.open('GET', uri);
    xhr.send();
  });
}

export async function readFileBase64(uri: string): Promise<string> {
  const normalizedUri = normalizeFileUri(uri);

  try {
    const response = await fetch(normalizedUri);
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    return blobToBase64(await response.blob());
  } catch {
    return readFileBase64XHR(normalizedUri);
  }
}

export async function fileToBase64(file: PickedFile): Promise<string> {
  const normalized = normalizeUploadFile(file);
  if (normalized.base64) return normalized.base64;
  return readFileBase64(normalized.uri);
}

function pickerError(response: ImagePickerResponse, label: string): boolean {
  if (response.didCancel) return true;
  if (response.errorCode) {
    Alert.alert(label, response.errorMessage ?? 'Could not open picker');
    return true;
  }
  return false;
}

async function requestCameraPermission(): Promise<boolean> {
  if (Platform.OS !== 'android') return true;

  const hasPermission = await PermissionsAndroid.check(PermissionsAndroid.PERMISSIONS.CAMERA);
  if (hasPermission) return true;

  const result = await PermissionsAndroid.request(PermissionsAndroid.PERMISSIONS.CAMERA, {
    title: 'Camera permission',
    message: 'Allow camera access to take photos for notes and assignments.',
    buttonPositive: 'Allow',
    buttonNegative: 'Cancel',
  });

  return result === PermissionsAndroid.RESULTS.GRANTED;
}

async function requestGalleryPermission(): Promise<boolean> {
  if (Platform.OS !== 'android') return true;

  if (Platform.Version >= 33) {
    const permission = PermissionsAndroid.PERMISSIONS.READ_MEDIA_IMAGES;
    const hasPermission = await PermissionsAndroid.check(permission);
    if (hasPermission) return true;
    const result = await PermissionsAndroid.request(permission, {
      title: 'Photos permission',
      message: 'Allow access to photos to attach images.',
      buttonPositive: 'Allow',
      buttonNegative: 'Cancel',
    });
    return result === PermissionsAndroid.RESULTS.GRANTED;
  }

  const permission = PermissionsAndroid.PERMISSIONS.READ_EXTERNAL_STORAGE;
  const hasPermission = await PermissionsAndroid.check(permission);
  if (hasPermission) return true;
  const result = await PermissionsAndroid.request(permission, {
    title: 'Storage permission',
    message: 'Allow access to photos to attach images.',
    buttonPositive: 'Allow',
    buttonNegative: 'Cancel',
  });
  return result === PermissionsAndroid.RESULTS.GRANTED;
}

export async function takePhoto(): Promise<PickedFile[]> {
  const allowed = await requestCameraPermission();
  if (!allowed) {
    Alert.alert('Permission needed', 'Camera access is required to take photos.');
    return [];
  }

  let result: ImagePickerResponse;
  try {
    result = await launchCamera({
      mediaType: 'photo',
      quality: 0.72,
      saveToPhotos: false,
      cameraType: 'back',
      includeBase64: true,
      assetRepresentationMode: 'compatible',
    });
  } catch {
    showPickerUnavailable();
    return [];
  }

  if (pickerError(result, 'Camera')) return [];

  return (result.assets ?? [])
    .map(asset => assetToFile(asset, 'photo'))
    .filter((file): file is PickedFile => file !== null);
}

export async function pickImagesFromGallery(): Promise<PickedFile[]> {
  const allowed = await requestGalleryPermission();
  if (!allowed) {
    Alert.alert('Permission needed', 'Photo library access is required to pick images.');
    return [];
  }

  let result: ImagePickerResponse;
  try {
    result = await launchImageLibrary({
      mediaType: 'photo',
      quality: 0.72,
      selectionLimit: 0,
      includeBase64: true,
      assetRepresentationMode: 'compatible',
    });
  } catch {
    showPickerUnavailable();
    return [];
  }

  if (pickerError(result, 'Gallery')) return [];

  return (result.assets ?? [])
    .map(asset => assetToFile(asset, 'image'))
    .filter((file): file is PickedFile => file !== null);
}
