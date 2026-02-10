<?php
function getProTip() {
    $tips = [
        "Quantify your achievements! Instead of 'Managed a team', say 'Managed a team of 5 and increased efficiency by 20%'.",
        "Tailor your CV to the job description. Use keywords from the ad to pass ATS scanners.",
        "Keep your CV to 1-2 pages maximum. Recruiters spend an average of 6 seconds scanning a resume.",
        "Use active verbs like 'Led', 'Developed', 'Created', and 'Optimized' instead of passive language.",
        "Include a link to your LinkedIn profile and portfolio. Ensure they are up-to-date.",
        "Avoid using generic buzzwords like 'Hard worker' or 'Team player'. Show, don't just tell.",
        "Proofread multiple times. a single typo can get your application rejected.",
        "Use a clean, professional email address. 'coolguy123@email.com' won't cut it.",
        "Focus on results, not just duties. What did you accomplish in your previous roles?",
        "Save your CV as a PDF unless specifically asked for Word. This ensures formatting stays consistent.",
        "If you have a gap in employment, be prepared to explain it positively (e.g., upskilling, freelancing).",
        "Don't include a photo unless it's standard practice in your country (e.g., parts of Europe/Asia).",
        "Highlight soft skills like communication and leadership with concrete examples.",
        "Put your most relevant experience first. If you're a recent grad, education can go top.",
        "Use bullet points for readability. Avoid long paragraphs of text.",
        "Include a 'Skills' section that lists technical and soft skills relevant to the role.",
        "Customize your professional summary for every application to hook the recruiter immediately.",
        "Network! referrals are often more effective than cold applying.",
        "Continuously update your CV, even when you aren't looking. It's harder to remember achievements later.",
        "Remove outdated references like 'References available upon request'. It's implied.",
        "Ensure your contact information is correct and easy to find at the top."
    ];

    return $tips[array_rand($tips)];
}
?>
