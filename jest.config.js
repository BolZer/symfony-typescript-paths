module.exports = {
    globals: {
        'ts-jest': {
            tsConfig: '/tsconfig.json'
        }
    },
    "roots": [
        "<rootDir>/Tests"
    ],
    "testMatch": [
        "**/__tests__/**/*.+(ts|tsx|js)",
        "**/?(*.)+(spec|test).+(ts|tsx|js)"
    ],
    "transform": {
        "^.+\\.(ts|tsx)$": "ts-jest"
    },
}