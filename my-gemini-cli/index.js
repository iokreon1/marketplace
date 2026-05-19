#!/usr/bin/env node

const { GoogleGenerativeAI } = require("@google/generative-ai");

// Mengambil API Key dari environment variable
const apiKey = process.env.GEMINI_API_KEY;
if (!apiKey) {
  console.error("❌ Error: GEMINI_API_KEY belum diatur di environment variable kamu!");
  process.exit(1);
}

// Mengambil argumen/perintah teks dari terminal
const prompt = process.argv.slice(2).join(" ");
if (!prompt) {
  console.error("💡 Cara pakai: gemini \"tulis pertanyaan kamu disini\"");
  process.exit(1);
}

const genAI = new GoogleGenerativeAI(apiKey);

async function main() {
  try {
    // Menggunakan model Gemini Pro
    const model = genAI.getGenerativeModel({ model: "gemini-2.5-flash" });
    
    console.log("⏳ Menunggu respon Gemini...");
    const result = await model.generateContent(prompt);
    const response = await result.response;
    
    console.log("\n--- RESPON GEMINI ---");
    console.log(response.text());
    console.log("---------------------\n");
  } catch (error) {
    console.error("❌ Terjadi kesalahan:", error.message);
  }
}

main();