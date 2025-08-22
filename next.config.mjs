/** @type {import('next').NextConfig} */
const nextConfig = {
  eslint: {
    ignoreDuringBuilds: true,
  },
  typescript: {
    ignoreBuildErrors: true,
  },
  images: {
    unoptimized: true,
  },
  // Move allowedDevOrigins to the root config
  allowedDevOrigins: [
    /^https?:\/\/.+$/, // allows any HTTP(S) origin
  ],
  experimental: {
    // your other experimental settings (if any)
  },
};

export default nextConfig;
