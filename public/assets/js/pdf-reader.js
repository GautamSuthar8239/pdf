// pdf-reader.js

async function extractPdfText(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = async function (e) {
            try {
                const typedArray = new Uint8Array(e.target.result);
                const pdf = await pdfjsLib.getDocument(typedArray).promise;

                let fullText = "";

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const content = await page.getTextContent();

                    const strings = content.items.map(item => item.str).join(" ");
                    fullText += strings + "\n";
                }

                resolve(fullText);
            } catch (error) {
                reject(error);
            }
        };

        reader.onerror = reject;

        reader.readAsArrayBuffer(file);
    });
}
