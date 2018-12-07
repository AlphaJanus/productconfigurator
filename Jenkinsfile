node {
    environment {
        CODACY_PROJECT_TOKEN = '65f6a75ca23a4b449b72a48dbafcb7fd'
    }
    // Clean workspace before doing anything
    deleteDir()
    try {
        stage ('Clone') {
            sh "composer create-project --repository=https://repo.magento.com magento/marketplace-eqp magento-coding-standard"
            sh "composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition:2.2.7 magento-build"
            sh "composer create-project codacy/coverage cc-reporter"
            sh "mkdir -p magento-build/app/code/Netzexpert/ProductConfigurator"
            dir ('magento-build/app/code/Netzexpert/ProductConfigurator') {
                checkout scm
            }
        }
        stage ('Build') {
            sh "echo 'shell scripts to build project...'"
            dir ('magento-build') {
                sh "../magento-coding-standard/vendor/bin/phpcs ./ --standard=MEQP2 --severity=10 --config-set m2-path ./"
            }
        }
        stage ('Test') {
            dir ('magento-build') {
                sh "vendor/bin/phpunit app/code/ -c dev/tests/unit/phpunit.xml.dist --coverage-html ../reports/coverage/ --coverage-clover ../reports/coverage/clover.xml"
            }
            step ([
                $class: 'CloverPublisher',
                cloverReportDir: 'reports/coverage/',
                cloverReportFileName: 'clover.xml',
                healthyTarget: [methodCoverage: 70, conditionalCoverage: 80, statementCoverage: 80], // optional, default is: method=70, conditional=80, statement=80
                unhealthyTarget: [methodCoverage: 50, conditionalCoverage: 50, statementCoverage: 50], // optional, default is none
                failingTarget: [methodCoverage: 0, conditionalCoverage: 0, statementCoverage: 0]     // optional, default is none
            ])
            dir ('magento-build/app/code/Netzexpert/ProductConfigurator') {
                commitId = sh(returnStdout: true, script: 'git rev-parse HEAD')
            }
            withEnv(["CODACY_PROJECT_TOKEN=f4359a5796054100922032556b5436df"]) {
                sh "cc-reporter/bin/codacycoverage clover reports/coverage/clover.xml --git-commit=${commitId}"
            }

        }
    } catch (err) {
        currentBuild.result = 'FAILED'
        throw err
    }
}
