node {
    // Clean workspace before doing anything
    deleteDir()
    try {
        stage ('Clone') {
            sh "composer create-project --repository=https://repo.magento.com magento/marketplace-eqp magento-coding-standard"
            sh "composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition:2.2.7 magento-build"
            sh "mkdir -p magento-build/app/code/Netzexpert/ProductConfigurator"
            dir ('magento-build/app/code/Netzexpert/ProductConfigurator') {
                checkout scm
            }
        }
        stage ('Build') {
            sh "echo 'shell scripts to build project...'"
            dir ('magento-build/app/code/Netzexpert/ProductConfigurator') {
                sh 'pwd'
                sh "../../../../../magento-coding-standard/vendor/bin/phpcs ./ --standard=MEQP2 --severity=10 --config-set m2-path ../../../../"
                sh "../../../../vendor/bin/phpunit ../../app/code/ -c magento-build/dev/tests/unit/phpunit.xml.dist"
            }
        }
    } catch (err) {
        currentBuild.result = 'FAILED'
        throw err
    }
}
